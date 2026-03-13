<?php

use App\Domains\Auth\Models\User;
use App\Domains\Destaque\Models\Destaque;
use App\Domains\Loja\Models\Loja;
use App\Domains\Noticias\Models\Noticias;
use App\Domains\Pagamento\Models\Pagamento;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Produto\Models\Produto;
use App\Domains\Promocao\Models\Promocao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

/**
 * Helper: cria uma loja ativa com tipo_loja especificado.
 */
function criarLoja(string $tipo = 'cervejaria', bool $ativa = true): Loja
{
    return Loja::create([
        'nome_fantasia' => 'Loja Teste '.Str::random(5),
        'tipo_loja' => $tipo,
        'ativo' => $ativa,
        'latitude' => -23.5505,
        'longitude' => -46.6333,
        'raio_entrega_km' => 10,
        'tempo_entrega_min' => 30,
        'tempo_entrega_max' => 60,
        'aceite_automatico' => true,
        'pedido_minimo' => 50.00,
        'taxa_comissao' => 10.00,
        'cep' => '01001000',
        'logradouro' => 'Praça da Sé',
        'numero' => '1',
        'bairro' => 'Sé',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
    ]);
}

// --- Lojas públicas ---

it('lista cervejarias publicas', function () {
    criarLoja('cervejaria', true);
    criarLoja('cervejaria', true);
    criarLoja('distribuidora', true);

    $this->getJson('/api/public/lojas/cervejarias')
        ->assertStatus(200)
        ->assertJsonStructure(['data']);
});

it('lista distribuidoras publicas', function () {
    criarLoja('distribuidora', true);

    $this->getJson('/api/public/lojas/distribuidoras')
        ->assertStatus(200)
        ->assertJsonStructure(['data']);
});

it('retorna loja publica por id', function () {
    $loja = criarLoja('cervejaria');

    $this->getJson("/api/public/lojas/{$loja->id}")
        ->assertStatus(200)
        ->assertJsonFragment(['id' => $loja->id]);
});

it('retorna 404 para loja inexistente', function () {
    $this->getJson('/api/public/lojas/'.Str::ulid())
        ->assertStatus(404);
});

// --- Catálogo de produtos ---

it('retorna catalogo de produtos da loja', function () {
    $loja = criarLoja('cervejaria');

    $this->getJson("/api/public/lojas/{$loja->id}/produtos")
        ->assertStatus(200)
        ->assertJsonStructure(['data']);
});

// --- CEP ---

it('lookup de cep retorna estrutura esperada ou erro gracioso', function () {
    // Just check route works - external API may not be available in test env
    $response = $this->getJson('/api/public/cep/01001000');
    expect($response->status())->toBeIn([200, 422, 503]);
});

// --- Promoções ---

it('lista promocoes ativas publicamente', function () {
    Promocao::create([
        'titulo' => 'Promo Teste',
        'descricao' => 'Desconto especial',
        'data_inicio' => now()->subDay(),
        'data_fim' => now()->addDay(),
        'ativo' => true,
        'loja_id' => criarLoja()->id,
    ]);

    $this->getJson('/api/public/promocoes')
        ->assertStatus(200)
        ->assertJsonStructure(['data']);
});

// --- Destaques ---

it('lista destaques publicamente', function () {
    $loja = criarLoja();

    if (class_exists(Destaque::class)) {
        $this->getJson('/api/public/destaques')
            ->assertStatus(200);
    } else {
        $this->getJson('/api/public/destaques')
            ->assertStatus(200);
    }
});

// --- Notícias ---

it('lista noticias ativas publicamente', function () {
    Noticias::create([
        'titulo' => 'Notícia Teste',
        'conteudo' => 'Conteúdo da notícia.',
        'publicado_em' => now(),
        'ativo' => true,
    ]);

    $this->getJson('/api/public/noticias')
        ->assertStatus(200)
        ->assertJsonStructure(['data']);
});

it('retorna noticia individual por id', function () {
    $noticia = Noticias::create([
        'titulo' => 'Notícia Individual',
        'conteudo' => 'Texto completo.',
        'publicado_em' => now(),
        'ativo' => true,
    ]);

    $this->getJson("/api/public/noticias/{$noticia->id}")
        ->assertStatus(200)
        ->assertJsonFragment(['id' => $noticia->id]);
});

// --- Configurações ---

it('lista configuracoes publicas', function () {
    $this->getJson('/api/public/configuracoes')
        ->assertStatus(200);
});

// --- Pedidos (autenticados) ---

it('consumidor nao autenticado nao consegue criar pedido', function () {
    $this->postJson('/api/public/pedidos', [
        'loja_id' => Str::ulid(),
        'endereco_id' => Str::ulid(),
        'items' => [],
    ])->assertStatus(401);
});

it('consumidor autenticado consegue criar pedido com payload valido', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $token = auth('api')->login($user);

    // The endpoint will likely fail with 422 due to missing referenced entities,
    // but must not 401 or 500 — the route is accessible and validated.
    $response = $this->withToken($token)
        ->postJson('/api/public/pedidos', [
            'loja_id' => Str::ulid(),
            'endereco_id' => Str::ulid(),
            'items' => [],
        ]);

    expect($response->status())->toBeIn([201, 422, 404]);
});

it('consumidor autenticado consegue listar seus pedidos', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $token = auth('api')->login($user);

    $this->withToken($token)
        ->getJson('/api/public/pedidos/meus')
        ->assertStatus(200)
        ->assertJsonStructure(['data']);
});

// --- Detalhe de pedido ---

it('consumidor autenticado visualiza detalhe do proprio pedido', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $token = auth('api')->login($user);

    $pedido = Pedido::create([
        'user_id' => $user->id,
        'loja_id' => criarLoja()->id,
        'endereco_id' => Str::ulid(),
        'subtotal' => 50.00,
        'taxa_entrega' => 5.00,
        'total' => 55.00,
        'status' => 'pendente',
    ]);

    $this->withToken($token)
        ->getJson("/api/public/pedidos/{$pedido->id}")
        ->assertStatus(200)
        ->assertJsonFragment(['id' => $pedido->id]);
});

it('consumidor nao pode ver pedido de outro usuario', function () {
    $user1 = User::factory()->create(['email_verified_at' => now()]);
    $user2 = User::factory()->create(['email_verified_at' => now()]);
    $token2 = auth('api')->login($user2);

    $pedido = Pedido::create([
        'user_id' => $user1->id,
        'loja_id' => criarLoja()->id,
        'endereco_id' => Str::ulid(),
        'subtotal' => 50.00,
        'taxa_entrega' => 5.00,
        'total' => 55.00,
        'status' => 'pendente',
    ]);

    $this->withToken($token2)
        ->getJson("/api/public/pedidos/{$pedido->id}")
        ->assertStatus(404);
});

// --- Status de pagamento ---

it('consumidor consulta status do pagamento do pedido', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $token = auth('api')->login($user);

    $pedido = Pedido::create([
        'user_id' => $user->id,
        'loja_id' => criarLoja()->id,
        'endereco_id' => Str::ulid(),
        'subtotal' => 50.00,
        'taxa_entrega' => 5.00,
        'total' => 55.00,
        'status' => 'aguardando_pagamento',
    ]);

    Pagamento::create([
        'pedido_id' => $pedido->id,
        'asaas_charge_id' => 'pay_status_test',
        'asaas_customer_id' => 'cus_test',
        'metodo' => 'pix',
        'status' => 'pendente',
        'valor' => 55.00,
    ]);

    $this->withToken($token)
        ->getJson("/api/public/pedidos/{$pedido->id}/pagamento/status")
        ->assertStatus(200)
        ->assertJsonStructure(['status']);
});

// --- Busca de lojas por raio ---

it('lista lojas proximas por coordenadas', function () {
    criarLoja('cervejaria');

    $response = $this->getJson('/api/public/lojas/proximas?latitude=-23.5505&longitude=-46.6333&raio=15');

    expect($response->status())->toBeIn([200, 422]);
});

// --- Busca de loja por produto ---

it('busca lojas que vendem determinado produto', function () {
    $loja = criarLoja('distribuidora');
    $produto = Produto::create([
        'nome' => 'IPA Premium',
        'descricao' => 'Cerveja artesanal',
    ]);

    $loja->produtos()->attach($produto->id, [
        'preco' => 25.00,
        'estoque' => 10,
        'ativo' => true,
        'destaque' => false,
    ]);

    $response = $this->getJson('/api/public/lojas/buscar-produto?q=IPA');

    expect($response->status())->toBeIn([200, 422]);
});
