# ADR-003: Estratégia de Sessão do Consumidor

**Status:** Aceito  
**Data:** 2025-01  
**Deciders:** Time Cerva Gelada

---

## Contexto

O site público (`cervagelada` Nuxt 3) precisa suportar dois tipos de usuário:

1. **Visitante (guest):** Navegação sem autenticação — busca de lojas, visualização de catálogo, leitura de conteúdo editorial
2. **Consumidor autenticado:** Realização de pedidos, histórico, avaliações, perfil

A principal necessidade é: **permitir browsing sem login**, mas **exigir autenticação no momento do checkout**.

---

## Decisão

### Autenticação: JWT via API

O site usa JWT emitido pela `back_api` (Tymon/tymon-jwt) para autenticação de consumidores. Ao fazer login, o frontend recebe o token e o armazena localmente.

**Armazenamento do token no cliente:**
- `localStorage` para persistência entre sessões (conveniente para "lembrar de mim")
- Ou `sessionStorage` para sessão temporária (mais seguro em dispositivos compartilhados)
- **O cookie não é usado** para JWT para evitar CSRF; a proteção CORS da API é suficiente

### Navegação como Visitante

- Nenhuma rota pública do site exige autenticação para renderização
- Endpoints públicos da API (lojas, produtos, notícias, promoções) são chamados sem token
- O componente `AgeVerificationModal` desafia o visitante antes de exibir conteúdo alcoólico
- A verificação de idade é feita via `localStorage` (`age_verified: true`) — não persistida no backend

### Fluxo de Checkout

```
Visitante navegando
    ↓
Adiciona produto ao carrinho (estado local/cookie)
    ↓
Inicia checkout
    ↓
[Não autenticado?] → Modal de Login/Registro → Login via POST /api/auth/login → JWT salvo
    ↓
[Autenticado] → POST /api/pedidos com Bearer token
    ↓
Confirmação de pedido
```

### Tokens e Expiração

- JWT expira em 1 hora por padrão (configurável em `config/jwt.php`)
- Refresh token disponível via `POST /api/auth/refresh` para renoválo sem novo login
- O Nuxt usa `useApi` composable que intercepta 401 e redireciona ao login

### Carrinho (Cart State)

- O carrinho é mantido no estado local do Nuxt (Pinia store) antes do checkout
- Não há persistência de carrinho no backend na Fase 0
- Carrinho é perdido ao fechar o browser se não autenticado (comportamento aceitável na Fase 0)

---

## Alternativas Consideradas

| Alternativa          | Razão para não escolher                           |
|----------------------|---------------------------------------------------|
| Sessão server-side   | Não combina com SSR stateless no Nuxt + API separada |
| Cookies HTTP-only    | Exigiria CSRF protection adicional na API        |
| OAuth / Social Login | Fora do escopo da Fase 0                         |

---

## Consequências

- **Visitantes** podem navegar e ver conteúdo sem atrito de login
- **SEO** não é comprometido: páginas públicas são rendizadas com SSR sem autenticação
- **Segurança**: dados sensíveis (pedidos, perfil) exigem JWT válido
- **Carrinho**: estado local na Fase 0; persistência no servidor é trabalho futuro (Fase 1)
- **LGPD/Maioridade**: verificação de idade é client-side na Fase 0; considerar validação server-side em Fase 1 para maior rigor
