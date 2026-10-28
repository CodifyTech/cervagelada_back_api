<?php

use App\Domains\ACL\Models\Permission;
use App\Domains\ACL\Models\Role;
use App\Domains\Auth\Models\User;
use App\Domains\Loja\Models\EntregadorLoja;
use App\Domains\Loja\Models\Loja;
use App\Domains\Pedido\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function criarLojaParaDespacho(string $nome): Loja
{
    return Loja::create([
        'nome_fantasia' => $nome,
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
        'aceite_automatico' => false,
        'pedido_minimo' => 0,
        'taxa_comissao' => 10,
        'ativo' => true,
    ]);
}

function criarUsuarioComPerfil(string $nome, string $perfil, ?string $lojaId = null): User
{
    // Sem eventos: o TenantScope sobrescreveria a loja_id com a do usuário autenticado
    $usuario = User::withoutEvents(fn () => User::forceCreate([
        'name' => $nome,
        'email' => fake()->unique()->safeEmail(),
        'password' => bcrypt('123456'),
        'email_verified_at' => now(),
        'loja_id' => $lojaId,
    ]));

    $usuario->roles()->attach(Role::firstOrCreate(['slug' => $perfil], ['name' => ucfirst($perfil)])->id);

    return $usuario;
}

beforeEach(function () {
    $this->loja = criarLojaParaDespacho('Loja Principal');
    $this->outraLoja = criarLojaParaDespacho('Outra Loja');

    $this->lojista = criarUsuarioComPerfil('Lojista', 'logista', $this->loja->id);
    Role::where('slug', 'logista')->first()->permissions()->sync(
        Permission::firstOrCreate(['slug' => 'pedido edit'], ['name' => 'pedido'])->id
    );
    $this->token = auth('api')->login($this->lojista);

    $this->cliente = criarUsuarioComPerfil('Cliente', 'consumidor');

    $this->pedido = Pedido::create([
        'user_id' => $this->cliente->id,
        'loja_id' => $this->loja->id,
        'subtotal' => 50,
        'taxa_entrega' => 5,
        'total' => 55,
        'status' => 'pronto',
        'pin_entrega' => '123456',
    ]);
});

it('lista apenas os entregadores da loja do pedido', function () {
    $daLoja = criarUsuarioComPerfil('Ana Entregadora', 'entregador', $this->loja->id);
    $vinculado = criarUsuarioComPerfil('Bruno Vinculado', 'entregador');
    $inativo = criarUsuarioComPerfil('Caio Inativo', 'entregador');
    criarUsuarioComPerfil('Dora de Outra Loja', 'entregador', $this->outraLoja->id);
    criarUsuarioComPerfil('Eva Funcionária', 'funcionario', $this->loja->id);

    EntregadorLoja::create(['user_id' => $vinculado->id, 'loja_id' => $this->loja->id, 'ativo' => true]);
    EntregadorLoja::create(['user_id' => $inativo->id, 'loja_id' => $this->loja->id, 'ativo' => false]);

    $resposta = $this->withToken($this->token)->getJson("/api/pedidos/{$this->pedido->id}/entregadores");

    $resposta->assertOk()->assertJsonCount(2, 'data');
    expect(collect($resposta->json('data'))->pluck('id')->all())
        ->toEqualCanonicalizing([$daLoja->id, $vinculado->id]);
});

it('exige permissão de edição de pedido para listar entregadores', function () {
    $semPermissao = criarUsuarioComPerfil('Sem permissão', 'consumidor', $this->loja->id);
    $tokenSemPermissao = auth('api')->login($semPermissao);

    $this->withToken($tokenSemPermissao)
        ->getJson("/api/pedidos/{$this->pedido->id}/entregadores")
        ->assertForbidden();
});

it('não despacha sem informar o entregador', function () {
    $this->withToken($this->token)
        ->patchJson("/api/pedidos/{$this->pedido->id}/status", ['status' => 'em_rota'])
        ->assertStatus(422)
        ->assertJsonPath('message', 'Informe o entregador para despachar o pedido.');

    expect($this->pedido->refresh()->status->value)->toBe('pronto');
});

it('não despacha com entregador de outra loja', function () {
    $deOutraLoja = criarUsuarioComPerfil('Dora', 'entregador', $this->outraLoja->id);

    $this->withToken($this->token)
        ->patchJson("/api/pedidos/{$this->pedido->id}/status", [
            'status' => 'em_rota',
            'entregador_id' => $deOutraLoja->id,
        ])
        ->assertStatus(422)
        ->assertJsonPath('message', 'O entregador informado não pertence a esta loja.');

    expect($this->pedido->refresh()->entregador_id)->toBeNull();
});

it('não despacha com usuário que não é entregador', function () {
    $funcionario = criarUsuarioComPerfil('Eva', 'funcionario', $this->loja->id);

    $this->withToken($this->token)
        ->patchJson("/api/pedidos/{$this->pedido->id}/status", [
            'status' => 'em_rota',
            'entregador_id' => $funcionario->id,
        ])
        ->assertStatus(422);
});

it('despacha atribuindo o entregador ao pedido', function () {
    $entregador = criarUsuarioComPerfil('Ana', 'entregador', $this->loja->id);

    $this->withToken($this->token)
        ->patchJson("/api/pedidos/{$this->pedido->id}/status", [
            'status' => 'em_rota',
            'entregador_id' => $entregador->id,
        ])
        ->assertOk()
        ->assertJsonPath('status', 'em_rota')
        ->assertJsonPath('entregador_id', $entregador->id);

    expect($this->pedido->refresh()->entregador_id)->toBe($entregador->id);
});

it('ignora entregador_id em transições que não são o despacho', function () {
    $this->pedido->update(['status' => 'aceito']);
    $entregador = criarUsuarioComPerfil('Ana', 'entregador', $this->loja->id);

    $this->withToken($this->token)
        ->patchJson("/api/pedidos/{$this->pedido->id}/status", [
            'status' => 'preparando',
            'entregador_id' => $entregador->id,
        ])
        ->assertOk();

    expect($this->pedido->refresh()->entregador_id)->toBeNull();
});

it('o entregador despachado enxerga o pedido nas suas entregas em rota', function () {
    $entregador = criarUsuarioComPerfil('Ana', 'entregador', $this->loja->id);
    Role::where('slug', 'entregador')->first()->permissions()->sync(
        collect(['minhas-entregas list'])->map(
            fn (string $slug) => Permission::firstOrCreate(['slug' => $slug], ['name' => 'minhas-entregas'])->id
        )
    );

    $this->withToken($this->token)->patchJson("/api/pedidos/{$this->pedido->id}/status", [
        'status' => 'em_rota',
        'entregador_id' => $entregador->id,
    ])->assertOk();

    $this->withToken(auth('api')->login($entregador))
        ->getJson('/api/entregas/em-rota')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $this->pedido->id);
});
