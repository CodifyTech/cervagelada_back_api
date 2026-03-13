<?php

use App\Domains\Auth\Models\User;
use App\Domains\Produto\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
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

    $response->assertStatus(200);
});
