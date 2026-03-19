<?php

use App\Domains\Auth\Models\User;
use App\Domains\Produto\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'email_verified_at' => now(),
    ]);
    $this->token = auth('api')->login($this->user);
});

it('lista produtos autenticado', function () {
    $response = $this->withToken($this->token)
        ->getJson('/api/produtos');

    $response->assertStatus(200);
});

it('cria produto com dados validos', function () {
    $payload = [
        'nome' => 'IPA Artesanal',
        'descricao' => 'Cerveja IPA artesanal da casa',
        'marca' => 'Cervejaria Teste',
        'teor_alcoolico' => 6.5,
        'volume_ml' => 500,
        'ean' => '7891234567890',
    ];

    $response = $this->withToken($this->token)
        ->postJson('/api/produtos', $payload);

    $response->assertStatus(201);
    $this->assertDatabaseHas('produtos', ['nome' => 'IPA Artesanal']);
});

it('retorna 422 ao criar produto sem nome', function () {
    $response = $this->withToken($this->token)
        ->postJson('/api/produtos', ['marca' => 'Sem Nome']);

    $response->assertStatus(422);
});

it('atualiza produto existente', function () {
    $produto = Produto::create([
        'nome' => 'Produto Original',
        'descricao' => 'Descrição original',
        'marca' => 'Marca Teste',
    ]);

    $response = $this->withToken($this->token)
        ->putJson("/api/produtos/{$produto->id}", [
            'nome' => 'Produto Atualizado',
            'marca' => 'Marca Teste',
        ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('produtos', ['nome' => 'Produto Atualizado']);
});

it('busca produto por EAN', function () {
    Produto::create([
        'nome' => 'Produto EAN',
        'marca' => 'Marca',
        'ean' => '7891111111111',
    ]);

    $response = $this->withToken($this->token)
        ->getJson('/api/produtos/ean/7891111111111');

    $response->assertStatus(200)
        ->assertJsonPath('exists', true)
        ->assertJsonPath('data.ean', '7891111111111');
});

it('busca produto por SKU', function () {
    Produto::create([
        'nome' => 'Produto SKU',
        'marca' => 'Marca',
        'sku' => 'SKU12345',
    ]);

    $response = $this->withToken($this->token)
        ->getJson('/api/produtos/sku/SKU12345');

    $response->assertStatus(200)
        ->assertJsonPath('exists', true)
        ->assertJsonPath('data.sku', 'SKU12345');
});

it('busca produto por Nome', function () {
    Produto::create([
        'nome' => 'Cerveja Especial XPTO',
        'marca' => 'Marca',
    ]);

    $response = $this->withToken($this->token)
        ->getJson('/api/produtos/nome/XPTO');

    $response->assertStatus(200)
        ->assertJsonPath('exists', true)
        ->assertJsonCount(1, 'data');
});

it('cervejaria pode criar novo produto', function () {
    $loja = \App\Domains\Loja\Models\Loja::create([
        'nome_fantasia' => 'Cervejaria Teste',
        'tipo_loja' => 'cervejaria',
        'latitude' => '-23.5505',
        'longitude' => '-46.6333',
        'raio_entrega_km' => 10,
        'tempo_entrega_min' => 30,
        'tempo_entrega_max' => 60,
        'aceite_automatico' => true,
        'pedido_minimo' => 50.00,
        'taxa_comissao' => 15.00,
        'ativo' => true,
        'cep' => '01001-001',
        'logradouro' => 'Rua Exemplo',
        'numero' => '1',
        'bairro' => 'Centro',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
    ]);
    $user = User::create([
        'name' => 'Cervejaria User',
        'email' => 'cervejaria@example.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'loja_id' => $loja->id,
        'email_verified_at' => now(),
    ]);
    $token = auth('api')->login($user);

    $payload = [
        'nome' => 'Nova Cerveja Cervejaria',
        'marca' => 'Marca',
        'preco' => 15.00,
        'estoque' => 10,
        'ean' => '7890000000001',
    ];

    $response = $this->withToken($token)
        ->postJson('/api/produtos', $payload);

    $response->assertStatus(201);
    $this->assertDatabaseHas('produtos', ['nome' => 'Nova Cerveja Cervejaria']);
});

it('distribuidor nao pode criar novo produto inexistente', function () {
    $loja = \App\Domains\Loja\Models\Loja::create([
        'nome_fantasia' => 'Distribuidor Teste',
        'tipo_loja' => 'distribuidor',
        'latitude' => '-23.5505',
        'longitude' => '-46.6333',
        'raio_entrega_km' => 10,
        'tempo_entrega_min' => 30,
        'tempo_entrega_max' => 60,
        'aceite_automatico' => true,
        'pedido_minimo' => 50.00,
        'taxa_comissao' => 15.00,
        'ativo' => true,
        'cep' => '01001-001',
        'logradouro' => 'Rua Exemplo',
        'numero' => '2',
        'bairro' => 'Centro',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
    ]);
    $user = User::create([
        'name' => 'Distribuidor User',
        'email' => 'distribuidor@example.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'loja_id' => $loja->id,
        'email_verified_at' => now(),
    ]);
    $token = auth('api')->login($user);

    $payload = [
        'nome' => 'Nova Cerveja Distribuidor',
        'marca' => 'Marca',
        'preco' => 12.00,
        'estoque' => 20,
        'ean' => '7890000000002',
    ];

    $response = $this->withToken($token)
        ->postJson('/api/produtos', $payload);

    // Should return 500 or error message from exception
    $response->assertStatus(500)
        ->assertJsonPath('error', 'Apenas lojas do tipo Cervejaria podem cadastrar novos produtos.');
});

it('distribuidor pode vincular produto existente', function () {
    $produto = Produto::create([
        'nome' => 'Produto Existente',
        'marca' => 'Marca',
        'ean' => '7899999999999',
    ]);

    $loja = \App\Domains\Loja\Models\Loja::create([
        'nome_fantasia' => 'Distribuidor Vinculo',
        'tipo_loja' => 'distribuidor',
        'latitude' => '-23.5505',
        'longitude' => '-46.6333',
        'raio_entrega_km' => 10,
        'tempo_entrega_min' => 30,
        'tempo_entrega_max' => 60,
        'aceite_automatico' => true,
        'pedido_minimo' => 50.00,
        'taxa_comissao' => 15.00,
        'ativo' => true,
        'cep' => '01001-001',
        'logradouro' => 'Rua Exemplo',
        'numero' => '3',
        'bairro' => 'Centro',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
    ]);
    $user = User::create([
        'name' => 'Distribuidor User Vinculo',
        'email' => 'dist_vinculo@example.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'loja_id' => $loja->id,
        'email_verified_at' => now(),
    ]);
    $token = auth('api')->login($user);

    $payload = [
        'ean' => '7899999999999',
        'preco' => 10.00,
        'estoque' => 50,
    ];

    $response = $this->withToken($token)
        ->postJson('/api/produtos', $payload);

    $response->assertStatus(201);
    $this->assertDatabaseHas('loja_produtos', [
        'loja_id' => $loja->id,
        'produto_id' => $produto->id,
    ]);
});
;
