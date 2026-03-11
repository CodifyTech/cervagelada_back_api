# PRD — CervaGelada Marketplace

**Versão:** 1.0
**Fase:** MVP (Fase 1)
**Produto:** Plataforma CervaGelada
**Tipo:** Marketplace + Delivery de Bebidas

---

# 1. Visão do Produto

O **CervaGelada** é uma plataforma de marketplace especializada em **cervejas e bebidas**, conectando consumidores a **adegas, distribuidoras e cervejarias artesanais**.

A proposta de valor é oferecer:

* descoberta de lojas próximas
* entrega rápida
* catálogo padronizado de bebidas
* experiência inspirada em apps de delivery

O modelo de negócio inclui **marketplace + monetização por destaque patrocinado + conteúdo patrocinado**.

---

# 2. Problema

Consumidores interessados em cervejas especiais e bebidas enfrentam problemas como:

* dificuldade em descobrir lojas próximas
* falta de marketplace especializado em cervejas
* experiência fragmentada entre apps e lojas físicas
* ausência de curadoria de produtos cervejeiros

O CervaGelada resolve isso centralizando **descoberta, compra e entrega** em uma única plataforma.

---

# 3. Objetivos do Produto

## Objetivo principal

Validar um **MVP funcional de marketplace cervejeiro com delivery**.

## Objetivos estratégicos

1. Validar modelo **marketplace + delivery**
2. Criar base inicial de sellers
3. Criar experiência simples e rápida
4. Construir base de catálogo padronizado

---

# 4. Métricas de Sucesso (KPIs)

### Produto

* Número de pedidos/dia
* Conversão visita → compra
* Tempo médio para finalizar pedido
* Ticket médio

### Marketplace

* Número de sellers ativos
* SKUs cadastrados
* Taxa de aceitação de pedidos

### Operação

* Tempo médio de entrega
* Taxa de cancelamento

---

# 5. Personas / Usuários

## 1 — Consumidor

Perfil:

* pessoa física
* consumidor de cerveja
* busca entrega rápida

Principais ações:

* descobrir lojas próximas
* comprar bebidas
* acompanhar pedido

---

## 2 — Adega / Distribuidora

Perfil:

* loja física de bebidas
* entrega rápida

Características:

* raio de entrega até **15 km**
* entrega média **até 35 min**

Principais ações:

* cadastrar produtos
* receber pedidos
* atualizar status

---

## 3 — Cervejaria Artesanal

Perfil:

* fabricante de cerveja

Características:

* venda direta ao consumidor
* entrega em até **24 horas**

---

## 4 — Administrador

Responsável por:

* aprovar sellers
* moderar conteúdo
* gerenciar destaques
* administrar notícias

---

# 6. Escopo do Produto (MVP)

## 6.1 Onboarding e Localização

### Funcionalidade

Confirmação inicial de endereço.

### Regras

* usuário informa endereço via:

  * CEP
  * geolocalização
* sistema valida área atendida

### Cenários

1️⃣ Usuário dentro da área atendida
2️⃣ Usuário fora da área

Mensagem padrão:

> “No momento não temos adegas disponíveis na sua localização.”

---

# 6.2 Marketplace de Lojas

A home do sistema exibirá **carrosséis de lojas**.

### Adegas / Distribuidoras

* listagem em carrossel
* filtro por raio de atuação

### Cervejarias artesanais

* carrossel separado
* raio até 15 km

---

# 6.3 Página da Loja

Ao acessar uma loja o usuário verá:

* lista de produtos
* preço
* quantidade
* prazo de entrega

---

# 6.4 Catálogo de Produtos

### Catálogo Padronizado

Base inicial:

* **+6000 SKUs**

Sellers podem:

* vincular produtos existentes

### Produtos próprios

Cervejarias podem cadastrar:

Campos obrigatórios:

* nome
* foto
* descrição
* preço
* estoque

Cadastro sujeito à **aprovação do administrador**.

---

# 6.5 Carrinho e Checkout

### Carrinho

Usuário pode:

* adicionar produtos
* alterar quantidades

---

### Fluxo de checkout

Fluxo máximo:

1️⃣ Loja
2️⃣ Carrinho
3️⃣ Confirmação

---

# 6.6 Pagamento

Gateway utilizado:

**Asaas**

Regras:

* pagamento antes do envio ao seller
* uso de webhooks para atualizar status

### Estados do pagamento

* aguardando pagamento
* pago
* recusado
* cancelado
* estornado

### Regras de negócio

* somente pedidos **pagos** são enviados ao seller
* pagamento falho → pedido não criado

---

# 6.7 Gestão de Pedidos

## Status do pedido

1️⃣ Pedido recebido
2️⃣ Pedido aceito
3️⃣ Em entrega
4️⃣ Entregue

---

# 6.8 Validação de Entrega

Entrega confirmada via **PIN**.

Opções:

* PIN definido pelo usuário
* últimos dígitos do telefone

Objetivo:

* evitar fraudes

---

# 6.9 Promoções

Sellers podem:

* marcar produtos como promocionais

A plataforma exibirá:

**carrossel de promoções**

---

# 6.10 Monetização

## Destaques patrocinados

Espaço publicitário para marcas.

Exemplo:

* **R$ 10.000 / 30 dias**

---

## Landing page de destaque

Pode conter:

* imagem
* vídeo
* descrição
* botão “Conhecer”

---

# 6.11 Conteúdo (News)

Plataforma terá um **feed de notícias cervejeiras**.

### Página de notícia

* conteúdo exibido dentro do app
* exibição da fonte da notícia

---

## Conteúdo patrocinado

Permitido publicar reportagens patrocinadas.

Obrigatório:

* identificação clara.

---

# 6.12 Institucional

### Contato

Tela com informações da empresa.

### Redes sociais

Carrossel com links:

* Instagram
* Facebook
* etc.

---

# 7. Requisitos Não Funcionais

## Usabilidade

Interface inspirada em:

* iFood
* Zé Delivery
* Netflix (carrosséis)

---

## Performance

* geolocalização em tempo real
* carregamento rápido de listas

---

## Escalabilidade

* expansão por regiões
* ativação gradual por bairros/cidades

---

## Segurança

* validação de entrega por PIN
* controle de acesso por perfil

Perfis:

* admin
* seller
* consumidor

---

## Compliance

* crédito obrigatório de fontes
* identificação de conteúdo patrocinado

---

# 8. Relatórios

A plataforma deve disponibilizar relatórios:

## Administrativos

* usuários
* sellers
* status de cadastros
* regiões

---

## Marketplace

* pedidos
* status
* itens mais vendidos
* ticket médio

---

## Segurança

* logs de acesso

---

# 9. Fora do Escopo (Fase 1)

Não será implementado nesta fase:

* programa de fidelidade
* avaliações e comentários
* assinaturas
* múltiplas integrações logísticas
* aplicativo mobile

---

# 10. Benchmarks

O produto deve se inspirar em:

**iFood**

* localização
* experiência de delivery

**Zé Delivery**

* foco em bebidas

**Mercado Livre**

* catálogo e matching de produtos

**Netflix**

* interface com carrosséis

---

# 11. Roadmap (Sugestão)

### Fase 1 — MVP

* marketplace
* catálogo
* checkout
* pagamentos
* pedidos

### Fase 2

* avaliações
* fidelidade
* analytics avançado

### Fase 3

* app mobile
* logística integrada
