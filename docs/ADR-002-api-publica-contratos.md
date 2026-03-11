# ADR-002: Contratos da API Pública

**Status:** Aceito  
**Data:** 2025-01  
**Deciders:** Time Cerva Gelada

---

## Contexto

O site público (`cervagelada` Nuxt 3) precisa consumir dados da API sem autenticação para viabilizar SEO (SSR) e navegação de visitantes. É necessário definir quais endpoints são públicos versus autenticados, e qual o formato padrão de resposta.

---

## Decisão

### Endpoints Públicos (sem autenticação)

Estes endpoints são acessíveis sem token JWT, destinados ao site público e a integrações futuras:

| Método | Endpoint                        | Descrição                           |
|--------|---------------------------------|-------------------------------------|
| GET    | `/api/lojas`                    | Listagem de lojas ativas            |
| POST   | `/api/lojas/search`             | Busca lojas por raio/filtros        |
| GET    | `/api/lojas/{id}/produtos`      | Catálogo de produtos de uma loja    |
| GET    | `/api/produtos`                 | Listagem de produtos (catálogo)     |
| GET    | `/api/produtos/ean/{ean}`       | Busca produto por código EAN        |
| GET    | `/api/noticias`                 | Listagem de notícias                |
| GET    | `/api/noticias/{id}`            | Detalhe de uma notícia              |
| GET    | `/api/promocoes`                | Listagem de promoções ativas        |
| POST   | `/api/auth/register`            | Cadastro de usuário                 |
| POST   | `/api/auth/login`               | Login (retorna JWT)                 |
| POST   | `/api/auth/forgot-password`     | Solicitar reset de senha            |
| POST   | `/api/auth/reset-password`      | Redefinir senha com token           |

### Endpoints Autenticados (JWT obrigatório)

Todos os demais endpoints exigem o header `Authorization: Bearer {token}`.

Exemplos relevantes para o consumidor:

| Método | Endpoint                      | Descrição                      |
|--------|-------------------------------|--------------------------------|
| GET    | `/api/auth/profile`           | Perfil do usuário autenticado  |
| PUT    | `/api/auth/profile`           | Atualizar perfil               |
| GET    | `/api/pedidos`                | Pedidos do usuário             |
| POST   | `/api/pedidos`                | Criar pedido                   |
| PATCH  | `/api/pedidos/{id}/status`    | Atualizar status do pedido     |
| GET    | `/api/avaliacoes`             | Avaliações                     |
| POST   | `/api/avaliacoes`             | Criar avaliação                |

---

## Formato Padrão de Resposta

Todas as respostas seguem o envelope definido pelo `BaseController`:

```json
{
  "data": { ... } | [...],
  "message": "string opcional",
  "errors": { ... }
}
```

**Códigos HTTP utilizados:**
- `200` — Sucesso em leituras e atualizações
- `201` — Recurso criado com sucesso
- `204` — Sucesso sem corpo (ex.: delete)
- `401` — Não autenticado
- `403` — Sem permissão (ACL)
- `404` — Recurso não encontrado
- `422` — Erro de validação (campo `errors` preenchido)
- `500` — Erro interno

---

## Paginação

Listagens retornam paginação padrão do Laravel:

```json
{
  "data": [...],
  "current_page": 1,
  "per_page": 15,
  "total": 100,
  "last_page": 7
}
```

---

## Versionamento

A API não possui prefixo de versão atualmente (`/api/...`). Quando necessário introduzir breaking changes, adotar `v2` como prefixo: `/api/v2/...`.

---

## Consequências

- O site Nuxt pode fazer SSR sem necessidade de token para páginas públicas
- Dados sensíveis (preços, estoque, pedidos) permanecem protegidos por JWT
- Mudanças em endpoints públicos devem ser retrocompatíveis ou versionadas
