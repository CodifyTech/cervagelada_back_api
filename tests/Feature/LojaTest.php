<?php

use App\Domains\Auth\Models\User;
use App\Domains\Loja\Models\Loja;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
    $this->token = auth('api')->login($this->user);
});

it('lista lojas autenticado', function () {
    $response = $this->withToken($this->token)
        ->getJson('/api/lojas');

    $response->assertStatus(200);
});

it('cria loja com dados validos', function () {
    $payload = [
        'nome_fantasia' => 'Cervejaria Teste',
        'tipo_loja' => 'cervejaria',
        'latitude' => -23.5505,
        'longitude' => -46.6333,
        'raio_entrega_km' => 10,
        'tempo_entrega_min' => 30,
        'tempo_entrega_max' => 60,
        'cep' => '01310100',
        'logradouro' => 'Av. Paulista',
        'numero' => '1000',
        'bairro' => 'Bela Vista',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
    ];

    $response = $this->withToken($this->token)
        ->postJson('/api/lojas', $payload);

    $response->assertStatus(201);
    $this->assertDatabaseHas('lojas', ['nome_fantasia' => 'Cervejaria Teste']);
});

it('retorna 422 ao criar loja sem nome_fantasia', function () {
    $response = $this->withToken($this->token)
        ->postJson('/api/lojas', ['tipo_loja' => 'cervejaria']);

    $response->assertStatus(422);
});

it('busca lojas por raio', function () {
    Loja::create([
        'nome_fantasia' => 'Loja Próxima',
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

    $response = $this->withToken($this->token)
        ->postJson('/api/lojas/search', [
            'latitude' => -23.551,
            'longitude' => -46.633,
        ]);

    $response->assertStatus(200);
});
