# Documento de Requisitos do Sistema – CervaGelada (Fase 1)

*M2T Tecnologia Ltda (CERVAGELADA) e CODIFYTECH Tecnologia de Informação Ltda.*

---

# 1. Visão Geral

O **CervaGelada** é um marketplace de delivery focado exclusivamente no universo cervejeiro, conectando **consumidores, adegas, distribuidoras e cervejarias artesanais**.

O diferencial do produto está na especialização em cervejas, rapidez de entrega, curadoria de conteúdo e monetização via destaques e conteúdo patrocinado.

Este documento consolida **todos os requisitos funcionais e não funcionais** levantados a partir da transcrição da reunião de definição da **Fase 1 do produto**.

---

# 2. Objetivos da Fase 1

* Lançar um **MVP funcional** focado em vendas de bebidas cervejeiras
* Validar o modelo de **marketplace + delivery**
* Garantir uma experiência **simples, rápida e inspirada em apps consolidados**

  * iFood
  * Zé Delivery
  * Rappi

---

# 3. Perfis de Usuários (Stakeholders)

## 3.1 Consumidor Final

* Pessoas físicas que compram cervejas, bebidas e mercadorias do hub cervejeiro
* Usa o site para descobrir lojas próximas, promoções e lançamentos

---

## 3.2 Adega / Distribuidora de Bebidas (Seller)

* Loja física que vende bebidas
* Entrega rápida (**até 35 minutos**)
* Atua em raio curto (**até 15 km do consumidor**)

---

## 3.3 Cervejaria Artesanal (Seller)

* Fabricante de cervejas
* Pode vender diretamente ao consumidor
* Entrega em até **24 horas**
* Atua em raio maior (**até 15 km**)

---

## 3.4 Administrador da Plataforma

* Aprova cadastros
* Modera conteúdo
* Gerencia destaques, banners e notícias

---

# 4. Escopo da Fase 1 (Funcionalidades Principais)

---

# 4.1 Onboarding e Localização

## RF-01 — Confirmação de Endereço Inicial

* Ao abrir o app, o usuário deve confirmar seu endereço:

  * via **CEP**
  * via **geolocalização**

Inspirado na experiência do **iFood**.

---

## RF-02 — Validação Contínua de Localização

Caso o usuário esteja em local diferente do endereço salvo, o sistema deve perguntar:

* Deseja comprar para o endereço salvo?
* Ou deseja confirmar um novo endereço?

Após validação:

* verificar **raio de atuação automaticamente**

---

## RF-03 — Mensagem de Área Não Atendida

Caso não existam lojas na região:

> “No momento não temos adegas disponíveis na sua localização.”

---

# 4.2 Marketplace de Lojas

## RF-04 — Listagem de Adegas / Distribuidoras

* Exibir lojas em **formato de carrossel**
* Mostrar apenas lojas **aprovadas dentro do raio permitido**

Parâmetro configurável:

* entre **5 km e 15 km**

---

## RF-05 — Listagem de Cervejarias Artesanais

* Exibir cervejarias em **carrossel separado**
* Raio de atuação: **até 15 km**

---

## RF-06 — Página da Loja (Vitrine)

Ao clicar em uma loja exibir:

* Lista de produtos
* Preço
* Quantidade
* Prazo de entrega

---

# 4.3 Catálogo de Produtos

## RF-07 — Catálogo Padronizado (SKUs)

* Plataforma possui base com **mais de 6.000 SKUs padronizados**
* Sellers podem **dar match** em produtos já existentes

---

## RF-08 — Cadastro de Produtos Próprios (Cervejarias)

Cervejarias artesanais podem cadastrar novos produtos.

Campos mínimos:

* Nome
* Foto
* Descrição
* Preço
* Estoque

Cadastro sujeito à **aprovação do administrador**.

---

## RF-09 — Descrição do Produto Sob Demanda

* Descrição detalhada fica **oculta**
* Usuário acessa apenas clicando em **“Descrição”**

  * modal ou pop-up

---

# 4.4 Carrinho e Checkout

## RF-10 — Carrinho de Compras

Usuário pode:

* Adicionar produtos
* Ajustar quantidades

Checkout deve:

* gerar pedido persistido no banco
* iniciar com status **“aguardando pagamento”**

---

## RF-11 — Finalização de Pedido

Fluxo simples em até **3 telas**:

1. Loja
2. Carrinho
3. Confirmação de pedido

---

# 4.5 Pedido, Entrega e Validação

## RF-XX — Meios de Pagamento (Integração Asaas)

Sistema deve integrar com **Asaas** como gateway oficial.

Regras:

* Pagamento ocorre **antes do envio ao seller**
* Sistema recebe **webhooks do Asaas**

Estados mínimos:

* Aguardando pagamento
* Pago
* Recusado
* Cancelado
* Estornado

---

### Regras adicionais

* Apenas pedidos **Pago** podem ser enviados para loja
* Se pagamento falhar → **pedido não deve ser criado**

Sistema deve armazenar:

* ID da cobrança no Asaas
* Método de pagamento
* Status da transação

Checkout deve:

* apresentar claramente os meios de pagamento
* fluxo rápido e simples (estilo apps de delivery)

---

## RF-12 — Envio de Pedido ao Seller

* Pedido enviado diretamente à **loja responsável**

---

## RF-13 — Status do Pedido

* Pedido recebido
* Pedido aceito
* Pedido em entrega
* Pedido entregue

---

## RF-14 — Validação de Entrega (PIN)

Entrega deve ser validada via **PIN**.

Opções:

* PIN definido pelo usuário
* Últimos dígitos do telefone

Objetivo:

* evitar fraudes e conflitos

---

# 4.6 Promoções

## RF-15 — Marcação de Produto Promocional

Seller pode marcar produto como **promocional**.

---

## RF-16 — Carrossel de Promoções

* Produtos promocionais aparecem automaticamente
* Ao clicar no produto → usuário é redirecionado para loja

---

# 4.7 Destaques (Monetização)

## RF-17 — Área de Destaques Patrocinados

Espaço vendido para marcas.

Exemplo de preço:

* **R$ 10.000 / 30 dias**

---

## RF-18 — Landing Page de Destaque

Conteúdo pode conter:

* Imagem
* Vídeo
* Descrição

Botão:

```
Conhecer
```

---

## RF-19 — Redirecionamento Inteligente para Compra

Ao clicar em **“Comprar”**:

Sistema deve:

* identificar lojas que possuem o produto
* selecionar automaticamente a **loja mais próxima**

---

# 4.8 News (Conteúdo)

## RF-20 — CervaGelada News

Feed de notícias sobre o **mercado cervejeiro**.

---

## RF-21 — Página Interna de Notícia

Conteúdo exibido **dentro do app**.

Não redirecionar para site externo.

Exibir fonte da notícia:

Exemplo:

* Valor Econômico

---

## RF-22 — Conteúdo Patrocinado

Possibilidade de publicar:

* reportagens patrocinadas

Deve haver identificação clara:

```
Conteúdo Patrocinado
```

---

# 4.9 Institucional

## RF-23 — Contato

Tela com informações de contato da **CervaGelada**.

---

## RF-24 — Redes Sociais

Carrossel:

```
Siga o CervaGelada
```

Com links para redes sociais.

---

# 5. Requisitos Não Funcionais

---

## 5.1 Usabilidade

* Interface simples e rápida
* Inspirada em:

  * iFood
  * Zé Delivery
  * Netflix (carrosséis)

---

## 5.2 Performance

* Listagens considerando **geolocalização em tempo real**
* Respostas rápidas para navegação

---

## 5.3 Escalabilidade

* Sistema preparado para expansão por regiões
* Ativação gradual por **bairros / cidades**

---

## 5.4 Segurança

* Validação de entrega por PIN
* Controle de acesso por perfil:

  * admin
  * seller
  * consumidor

---

## 5.5 Conteúdo e Compliance

* Créditos obrigatórios para fontes de notícias
* Identificação clara de conteúdos patrocinados

---

## 5.6 Relatórios e Gráficos

Plataforma deverá disponibilizar relatórios:

### Relatórios Administrativos

* usuários
* sellers
* status de cadastros
* produtos
* regiões de atuação

### Relatórios de Marketplace

* pedidos
* status
* itens mais vendidos
* ticket médio

### Relatórios de Segurança

* logs de acesso

Os relatórios devem ter caráter:

* **gerencial**
* **operacional**

---

# 6. Fora do Escopo da Fase 1

* Programa de fidelidade
* Avaliações e comentários
* Assinaturas
* Integração com múltiplos meios logísticos
* Download de aplicativo (apenas conceitual nesta fase)

---

# 7. Benchmarks Obrigatórios

* iFood (localização e delivery)
* Zé Delivery (cerveja e rapidez)
* Mercado Livre (catálogo e match de produtos)
* Netflix (uso de carrosséis)
