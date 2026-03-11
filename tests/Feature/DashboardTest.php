<?php

use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['email_verified_at' => now()]);
    $this->token = auth('api')->login($this->user);
});

it('retorna metricas do dashboard autenticado', function () {
    $response = $this->withToken($this->token)
        ->getJson('/api/metricas');

    $response->assertStatus(200);
});

it('retorna vendas mensais autenticado', function () {
    $response = $this->withToken($this->token)
        ->getJson('/api/vendas-mensais');

    $response->assertStatus(200);
});

it('exige autenticacao para acessar dashboard', function () {
    $response = $this->getJson('/api/metricas');

    $response->assertStatus(401);
});
