<?php

use App\Domains\Auth\Models\User;
use App\Domains\Loja\Models\Loja;
use App\Domains\Pedido\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
    $this->token = auth('api')->login($this->user);

    $this->loja = Loja::create([
        'nome_fantasia' => 'Loja Teste',
        'tipo_loja' => 'cervejaria',
        'latitude' => -23.5505,
        'longitude' => -46.6333,
        'raio_entrega_km' => 10,
        'tempo_entrega_min' => 30,
        'tempo_entrega_max' => 60,
        'cep' => '01310100',
        'logradouro' => 'Av. Paulista',
        'numero' => '1',
        'bairro' => 'Bela Vista',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'ativo' => true,
    ]);
});

it('lista pedidos autenticado', function () {
    $response = $this->withToken($this->token)
        ->getJson('/api/pedidos');

    $response->assertStatus(200);
});

it('cria pedido com dados validos', function () {
    $payload = [
        'user_id' => $this->user->id,
        'loja_id' => $this->loja->id,
        'subtotal' => 50.00,
        'total' => 55.00,
        'status' => 'pendente',
    ];

    $response = $this->withToken($this->token)
        ->postJson('/api/pedidos', $payload);

    $response->assertStatus(201);
    $this->assertDatabaseHas('pedidos', [
        'user_id' => $this->user->id,
        'loja_id' => $this->loja->id,
        'status' => 'pendente',
    ]);
});

it('atualiza status do pedido', function () {
    $pedido = Pedido::create([
        'user_id' => $this->user->id,
        'loja_id' => $this->loja->id,
        'subtotal' => 50.00,
        'total' => 55.00,
        'status' => 'pendente',
    ]);

    $response = $this->withToken($this->token)
        ->patchJson("/api/pedidos/{$pedido->id}/status", [
            'status' => 'em_preparacao',
        ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('pedidos', [
        'id' => $pedido->id,
        'status' => 'em_preparacao',
    ]);
});

it('retorna 422 ao criar pedido sem loja_id', function () {
    $response = $this->withToken($this->token)
        ->postJson('/api/pedidos', [
            'user_id' => $this->user->id,
            'subtotal' => 50.00,
            'total' => 55.00,
        ]);

    $response->assertStatus(422);
});

it('exige autenticacao para acessar pedidos', function () {
    $response = $this->getJson('/api/pedidos');

    $response->assertStatus(401);
});
