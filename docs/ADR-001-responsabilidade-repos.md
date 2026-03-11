# ADR-001: Responsabilidade dos Repositórios

**Status:** Aceito  
**Data:** 2025-01  
**Deciders:** Time Cerva Gelada

---

## Contexto

O projeto é composto por três repositórios distintos que precisam ter seus limites de responsabilidade bem definidos para evitar acoplamento desnecessário e facilitar o desenvolvimento paralelo.

## Repositórios e Responsabilidades

### `cervagelada_back_api` — API Backend (Laravel 11)

**Responsabilidade:** API REST centralizada. Única fonte de verdade para dados de negócio.

- Autenticação e autorização (JWT, CASL, HasACL)
- Domínios de negócio: Auth, Loja, Produto, Pedido, ItemPedido, Avaliacao, TransacoesFinanceiras, Dashboard
- Regras de negócio, validações e cálculos
- Persistência (MySQL) e migrações
- Seeders de desenvolvimento
- Testes de integração (Pest)

**NÃO É responsabilidade desta aplicação:**
- Renderização de HTML/CSS
- Lógica de UI ou estado de navegação
- Assets estáticos para o consumidor final

---

### `cervagelada_frontend` — Painel Administrativo (Vue 3 + Vuetify)

**Responsabilidade:** Interface de gestão interna para administradores e lojistas.

- CRUD de todas as entidades via API REST
- Controle de acesso frontend (CASL com subjects/actions espelhando a API)
- Gerenciamento de usuários, lojas, produtos, pedidos, avaliações e transações
- Relatórios e dashboard analítico

**NÃO É responsabilidade desta aplicação:**
- Servir conteúdo para o consumidor final
- SEO ou tráfego orgânico
- Experiência de compra do cliente

---

### `cervagelada` — Site Público (Nuxt 3 + Tailwind)

**Responsabilidade:** Aplicação voltada ao consumidor final.

- Navegação como visitante (sem autenticação obrigatória)
- Busca e visualização de lojas, produtos, promoções e notícias
- Fluxo de pedido do consumidor (autenticação opcional para checkout)
- SEO: SSR/SSG para indexação por mecanismos de busca
- Conteúdo editorial: blog, revista, eventos

**NÃO É responsabilidade desta aplicação:**
- Gestão administrativa de dados
- Painel de controle para lojistas

---

## Matriz de Responsabilidade

| Funcionalidade              | back_api | frontend (admin) | cervagelada (público) |
|-----------------------------|----------|------------------|-----------------------|
| Auth JWT                    | ✅       | consume          | consume               |
| CRUD Lojas                  | ✅       | ✅               | read-only             |
| CRUD Produtos               | ✅       | ✅               | read-only             |
| Pedidos (gestão)            | ✅       | ✅               | —                     |
| Pedidos (consumidor)        | ✅       | —                | ✅                    |
| Dashboard/Métricas          | ✅       | ✅               | —                     |
| SEO / SSR                   | —        | —                | ✅                    |
| Verificação de idade        | —        | —                | ✅                    |

---

## Decisão

Cada repositório consome a `back_api` via HTTP. Não há chamadas diretas entre frontend e cervagelada. A API é a única camada que acessa o banco de dados.

## Consequências

- Qualquer nova feature de negócio exige alteração na `back_api` primeiro
- Os frontends podem evoluir independentemente desde que os contratos de API sejam respeitados
- Refatorações internas na API não devem quebrar os contratos públicos
