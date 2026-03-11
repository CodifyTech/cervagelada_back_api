<?php

use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('realiza login com credenciais validas', function () {
    $user = User::factory()->create([
        'email' => 'test@cervagelada.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@cervagelada.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['token']);
});

it('rejeita login com senha incorreta', function () {
    User::factory()->create([
        'email' => 'test@cervagelada.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@cervagelada.com',
        'password' => 'senhaerrada',
    ]);

    $response->assertStatus(401);
});

it('registra novo usuario com dados validos', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Novo Usuário',
        'email' => 'novo@cervagelada.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['token']);

    $this->assertDatabaseHas('users', ['email' => 'novo@cervagelada.com']);
});

it('retorna perfil do usuario autenticado', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $token = auth('api')->login($user);

    $response = $this->withToken($token)
        ->getJson('/api/auth/profile');

    $response->assertStatus(200)
        ->assertJsonFragment(['email' => $user->email]);
});

it('realiza logout invalidando o token', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $token = auth('api')->login($user);

    $response = $this->withToken($token)
        ->postJson('/api/auth/logout');

    $response->assertStatus(200);
});
