# Plano — Fulltext Search no campo `nome` de Produtos

## 1. Situação atual (diagnóstico)

Hoje existem **dois pontos** de busca por `nome` em `produtos`, ambos usando `LIKE "%valor%"` (busca por substring, sem relevância, sem ordenação por match):

1. **`ProdutoController::searchByField`** (`app/Domains/Produto/Controllers/ProdutoController.php:45`)
   Rota: `GET /produtos/{tipo}/{valor}` (usada pelo formulário de cadastro, `ProdutoService.ts::searchByName`, para checar se um produto já existe pelo nome ao vincular à loja).
   ```php
   case 'nome':
       $query->where('nome', 'like', "%{$valor}%");
   ```
   Sem `orderBy` — retorna em ordem arbitrária do banco.

2. **`BaseService::search`** (`app/Domains/Shared/Services/BaseService.php:148`), herdado por `ProdutoService`, exposto por `BaseController::search` na rota `POST /produtos/search`.
   Usado pela barra de busca genérica da listagem (`src/pages/produto/index.vue`, componente `SearchBar.vue`, campo `searchField = 'nome'`).
   ```php
   $query->where($field, 'like', "%$value%")->orderBy($field);
   ```
   Esse método é **genérico e compartilhado por todos os domínios** (não é exclusivo de Produto) — qualquer alteração aqui impacta todo o sistema, então não deve ser modificado para um comportamento específico de fulltext.

**Banco de dados:** `config/database.php` usa `mariadb` como default (`utf8mb4_uca1400_ai_ci`, já *accent-insensitive* e *case-insensitive*). Os testes automatizados rodam em **SQLite in-memory** (`phpunit.xml`). Isso é relevante porque o recurso `FULLTEXT` do Laravel (`whereFullText`) só é suportado nativamente em MySQL/MariaDB e Postgres — **não existe em SQLite**. Qualquer implementação precisa de um fallback para os testes não quebrarem.

---

## 2. Onde mexer: backend e frontend?

- **Banco de dados (migration):** sim, precisa. É necessário criar um **índice FULLTEXT** na coluna `nome` para o MySQL/MariaDB usar `MATCH ... AGAINST` em vez de `LIKE`. Não dá pra reaproveitar a migration antiga (`2026_01_06_..._create_produtos_table.php`) porque ela já foi executada em ambientes existentes — precisa de uma **nova migration** (padrão do projeto: migrations vivem em `app/Domains/Produto/Migrations/`, autodescobertas pelo `MigrationServiceProvider`).
- **Backend (Controller/Service):** sim, é o essencial do trabalho — trocar `LIKE` por `MATCH AGAINST` (via `whereFullText` do Eloquent) nos dois pontos listados acima, com fallback para `LIKE` quando a conexão não suporta fulltext (SQLite/testes).
- **Frontend:** **não precisa de mudança estrutural.** Os dois fluxos (`searchByName` e a busca da listagem) já mandam só a string digitada (`nome`/`value`) e já sabem renderizar a lista de resultados devolvida. A melhoria de relevância/ordenação é transparente para quem consome a API. Only ponto opcional (ver seção 6) é ajustar o tamanho mínimo do termo de busca na UI, por causa de uma particularidade do FULLTEXT explicada abaixo.

---

## 3. Abordagem técnica recomendada

Usar o **FULLTEXT nativo do MySQL/MariaDB**, em modo **BOOLEAN** com wildcard de prefixo por palavra (`+termo*`), porque:
- Dá relevância/ordenação de graça (`MATCH() AGAINST()` retorna um score, dá pra `orderBy` por ele).
- Modo boolean com `*` no final de cada palavra aproxima o comportamento atual de "contém" (prefixo), permitindo múltiplas palavras (`+ipa* +artesanal*` casa com "IPA Artesanal Puro").
- É a opção correta para bancos com um volume de produtos que tende a crescer — `LIKE '%...%'` não usa índice nenhum e escaneia a tabela inteira.

**Trade-off a aceitar conscientemente:** FULLTEXT em modo boolean com `*` casa **prefixo de palavra**, não substring livre no meio da palavra. Ex.: buscar por `erveja` não vai mais achar "Cerveja" (antes achava, por ser substring). Buscar por `cerv` continua achando "Cerveja". Isso é o comportamento padrão de qualquer fulltext search e normalmente é aceitável/esperado pelo usuário; vale alinhar essa expectativa antes de implementar.

**Risco de configuração do servidor:** o InnoDB tem `innodb_ft_min_token_size` (default = 3). Palavras com 1-2 caracteres (comuns em nomes curtos de cerveja, tipo "IPA" tem 3, ok, mas siglas de 2 letras ficariam de fora) não entram no índice. Vale checar essa configuração no MariaDB de produção/homologação antes de ir pra produção; se precisar, reduzir para 2 (requer `OPTIMIZE TABLE` ou rebuild do índice depois de mudar a variável).

---

## 4. Passo a passo

### 4.1 Migration — novo índice FULLTEXT

Novo arquivo em `app/Domains/Produto/Migrations/2026_XX_XX_add_fulltext_index_to_produtos_nome.php`:

```php
public function up(): void
{
    if (Schema::getConnection()->getDriverName() !== 'sqlite') {
        Schema::table('produtos', function (Blueprint $table) {
            $table->fullText('nome');
        });
    }
}

public function down(): void
{
    if (Schema::getConnection()->getDriverName() !== 'sqlite') {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropFullText(['nome']);
        });
    }
}
```

O guard por driver evita quebrar o `RefreshDatabase`/migrate dos testes em SQLite (que não suporta `fullText()` do schema builder).

### 4.2 `ProdutoController::searchByField` — caso `'nome'`

Trocar o `LIKE` por fulltext com fallback, e adicionar `orderBy` por relevância:

```php
case 'nome':
    if (DB::connection()->getDriverName() === 'sqlite') {
        $query->where('nome', 'like', "%{$valor}%");
    } else {
        $query->whereFullText('nome', $valor, ['mode' => 'boolean'])
              ->orderByRaw('MATCH(nome) AGAINST(? IN BOOLEAN MODE) DESC', [$valor]);
    }
    break;
```

(o `IN BOOLEAN MODE` com wildcard de prefixo por palavra deve ser montado a partir do termo — ver helper no item 4.4).

### 4.3 `ProdutoService` — busca da listagem (`nome`)

`BaseService::search` é genérico e não pode virar fulltext-only. A solução é **sobrescrever `search()` em `ProdutoService`**, chamando o `parent::search()` normalmente para qualquer campo, mas interceptando o caso `field === 'nome'` para usar fulltext:

```php
public function search(array $options = [], ?\Closure $builderCallback = null)
{
    if (($options['field'] ?? null) === 'nome' && DB::connection()->getDriverName() !== 'sqlite') {
        $value = $options['value'] ?? '';
        $builderCallback = function ($query) use ($value, $builderCallback) {
            $query->whereFullText('nome', $value, ['mode' => 'boolean'])
                  ->orderByRaw('MATCH(nome) AGAINST(? IN BOOLEAN MODE) DESC', [$value]);
            if ($builderCallback) {
                $builderCallback($query);
            }
        };
        // remove field/value pra não cair no where('nome','like',...) do parent
        unset($options['field'], $options['value']);
    }

    return parent::search($options, $builderCallback);
}
```
*(ajustar detalhes conforme a assinatura real de `BaseService::search`, que já aceita `$builderCallback` — não precisa reimplementar paginação/sort.)*

### 4.4 Helper para montar a query booleana

Criar um pequeno helper (ex.: em `App\Domains\Shared\Utils` ou dentro do próprio `ProdutoService`) que:
- Faz `trim`/`explode` por espaço no termo digitado.
- Escapa caracteres especiais do modo boolean (`+ - > < ( ) ~ * " @`).
- Monta `+palavra1* +palavra2*` (AND entre palavras, prefixo em cada uma).

Isso evita erro de sintaxe SQL quando o usuário digita algo como `"cerveja (ipa)"`.

### 4.5 Testes

- Ajustar `tests/Feature/ProdutoTest.php` (linha ~99-104, teste de busca por nome via `GET /api/produtos/nome/XPTO`): como os testes rodam em SQLite, o fallback `LIKE` mantém o teste passando sem alteração — só validar que o fallback está sendo de fato exercitado.
- Adicionar um teste novo cobrindo múltiplas palavras (ex.: buscar "Especial XPTO" deve achar "Cerveja Especial XPTO").
- Fulltext real (MATCH/AGAINST) **não é testável em SQLite** — se quiser cobertura de fato do caminho MySQL, precisa de um ambiente de teste com MySQL/MariaDB real (fora do escopo do `phpunit.xml` atual) ou aceitar que esse trecho fica coberto só pelo fallback nos testes automatizados, validado manualmente/homologação no banco real.

### 4.6 Frontend (ajustes opcionais, não obrigatórios)

Nenhuma mudança é necessária para o fluxo funcionar. Dois ajustes **opcionais** de UX, caso quiram alinhar a experiência ao novo comportamento:
- `SearchBar.vue` / `src/pages/produto/index.vue`: poderia adicionar um mínimo de 2-3 caracteres antes de disparar a busca (evita mandar termos muito curtos que o FULLTEXT vai ignorar de qualquer forma pelo `min_token_size`).
- Nenhuma mudança de contrato de API (mesmos campos de request/response), então `ProdutoService.ts` e `useProdutoStore.ts` continuam iguais.

---

## 5. Checklist de execução

- [ ] Criar migration com índice `FULLTEXT` em `produtos.nome` (guardada por driver)
- [ ] Rodar migration em ambiente de homologação com MariaDB e validar `SHOW INDEX FROM produtos`
- [ ] Criar helper de montagem de query boolean (escape + wildcard de prefixo)
- [ ] Atualizar `ProdutoController::searchByField` (caso `nome`)
- [ ] Sobrescrever `ProdutoService::search` para interceptar `field === 'nome'`
- [ ] Rodar `php artisan test --compact --filter=Produto` (garante que o fallback SQLite não quebrou nada)
- [ ] Validar manualmente no MariaDB: busca por palavra única, múltiplas palavras, termo com 1-2 caracteres, termo com caracteres especiais
- [ ] Checar `innodb_ft_min_token_size` no MariaDB de produção/homologação
- [ ] (Opcional) ajustar mínimo de caracteres no `SearchBar.vue`
- [ ] `./vendor/bin/pint --dirty --format agent` nos arquivos PHP alterados

---

## 6. Riscos e pontos de atenção

| Risco | Mitigação |
|---|---|
| Testes automatizados rodam em SQLite, que não suporta FULLTEXT | Fallback por `getDriverName()` para `LIKE`, código de fulltext só roda em MySQL/MariaDB |
| Mudança de comportamento: substring no meio da palavra deixa de casar | Aceitar como trade-off natural de fulltext; comunicar ao time/QA |
| `innodb_ft_min_token_size` pode excluir palavras/siglas curtas | Checar variável no servidor de produção antes do deploy; ajustar se necessário |
| Caracteres especiais do usuário quebram a sintaxe do modo boolean | Helper de escape antes de montar a query `MATCH AGAINST` |
| `BaseService::search` é compartilhado por todos os domínios | Não alterar a classe base — a lógica fulltext fica isolada em `ProdutoService::search` (override) |
