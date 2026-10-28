<?php

use App\Domains\ACL\Models\Permission;
use App\Domains\ACL\Models\Role;
use App\Domains\Auth\Models\User;
use App\Domains\Endereco\Models\Endereco;
use App\Domains\Loja\Models\Loja;
use App\Domains\Pedido\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Cria um usuário verificado (a factory padrão não resolve o model em Domains).
 */
function criarUsuario(): User
{
    return User::forceCreate([
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'password' => bcrypt('123456'),
        'email_verified_at' => now(),
    ]);
}

/**
 * Cria um pedido atribuído a um entregador.
 */
function criarPedidoDeEntrega(Loja $loja, User $cliente, ?User $entregador, string $status = 'em_rota', string $pin = '123456'): Pedido
{
    $endereco = Endereco::create([
        'apelido' => 'Casa',
        'cep' => '01310100',
        'logradouro' => 'Av. Paulista',
        'numero' => '100',
        'bairro' => 'Bela Vista',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'latitude' => -23.5612,
        'longitude' => -46.6558,
        'principal' => true,
        'user_id' => $cliente->id,
    ]);

    return Pedido::create([
        'user_id' => $cliente->id,
        'loja_id' => $loja->id,
        'entregador_id' => $entregador?->id,
        'endereco_id' => $endereco->id,
        'subtotal' => 50,
        'taxa_entrega' => 5,
        'total' => 55,
        'status' => $status,
        'pin_entrega' => $pin,
    ]);
}

beforeEach(function () {
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
        'aceite_automatico' => false,
        'pedido_minimo' => 0,
        'taxa_comissao' => 10,
        'ativo' => true,
    ]);

    $permissoes = collect(['minhas-entregas read', 'minhas-entregas list', 'minhas-entregas edit'])
        ->map(fn (string $slug) => Permission::firstOrCreate(
            ['slug' => $slug],
            ['name' => 'minhas-entregas']
        )->id);

    $perfil = Role::firstOrCreate(['slug' => 'entregador'], ['name' => 'Entregador']);
    $perfil->permissions()->sync($permissoes);

    $this->entregador = criarUsuario();
    $this->entregador->roles()->attach($perfil->id);
    $this->token = auth('api')->login($this->entregador);

    $this->cliente = criarUsuario();
    $this->cliente->forceFill(['telefone' => '11999990000'])->save();
});

it('exige autenticação', function () {
    app('auth')->forgetGuards();
    app('tymon.jwt')->unsetToken();

    $this->getJson('/api/entregas/atuais')->assertUnauthorized();
});

it('bloqueia usuário sem permissão de entrega', function () {
    $semPermissao = criarUsuario();
    $semPermissao->roles()->attach(Role::firstOrCreate(['slug' => 'consumidor'], ['name' => 'Consumidor'])->id);
    $tokenSemPermissao = auth('api')->login($semPermissao);

    $this->withToken($tokenSemPermissao)
        ->getJson('/api/entregas/atuais')
        ->assertForbidden();
});

it('lista apenas os pedidos atribuídos ao entregador', function () {
    $outroEntregador = criarUsuario();

    $meu = criarPedidoDeEntrega($this->loja, $this->cliente, $this->entregador, 'pronto');
    criarPedidoDeEntrega($this->loja, $this->cliente, $outroEntregador, 'pronto');
    criarPedidoDeEntrega($this->loja, $this->cliente, null, 'pronto');
    criarPedidoDeEntrega($this->loja, $this->cliente, $this->entregador, 'entregue');

    $resposta = $this->withToken($this->token)->getJson('/api/entregas/atuais');

    $resposta->assertOk()->assertJsonCount(1, 'data');
    expect($resposta->json('data.0.id'))->toBe($meu->id);
});

it('lista somente pedidos em rota na tela de em rota', function () {
    criarPedidoDeEntrega($this->loja, $this->cliente, $this->entregador, 'pronto');
    $emRota = criarPedidoDeEntrega($this->loja, $this->cliente, $this->entregador, 'em_rota');

    $resposta = $this->withToken($this->token)->getJson('/api/entregas/em-rota');

    $resposta->assertOk()->assertJsonCount(1, 'data');
    expect($resposta->json('data.0.id'))->toBe($emRota->id);
});

it('nunca expõe o PIN nas respostas do entregador', function () {
    $pedido = criarPedidoDeEntrega($this->loja, $this->cliente, $this->entregador);

    $this->withToken($this->token)->getJson('/api/entregas/em-rota')
        ->assertOk()
        ->assertJsonMissingPath('data.0.pin_entrega');

    $this->withToken($this->token)->getJson("/api/entregas/{$pedido->id}")
        ->assertOk()
        ->assertJsonMissingPath('pin_entrega')
        ->assertJsonPath('endereco.logradouro', 'Av. Paulista')
        ->assertJsonPath('user.id', $this->cliente->id)
        ->assertJsonPath('user.telefone', '11999990000');
});

it('retorna 404 ao consultar pedido de outro entregador', function () {
    $outroEntregador = criarUsuario();
    $pedido = criarPedidoDeEntrega($this->loja, $this->cliente, $outroEntregador);

    $this->withToken($this->token)->getJson("/api/entregas/{$pedido->id}")->assertNotFound();
});

it('lista o histórico com entregues e cancelados', function () {
    criarPedidoDeEntrega($this->loja, $this->cliente, $this->entregador, 'entregue');
    criarPedidoDeEntrega($this->loja, $this->cliente, $this->entregador, 'cancelado');
    criarPedidoDeEntrega($this->loja, $this->cliente, $this->entregador, 'em_rota');

    $this->withToken($this->token)->getJson('/api/entregas/historico')
        ->assertOk()
        ->assertJsonPath('total', 2);

    $this->withToken($this->token)->getJson('/api/entregas/historico?status=entregue')
        ->assertOk()
        ->assertJsonPath('total', 1);
});

it('retorna o resumo do entregador', function () {
    criarPedidoDeEntrega($this->loja, $this->cliente, $this->entregador, 'pronto');
    criarPedidoDeEntrega($this->loja, $this->cliente, $this->entregador, 'em_rota');

    $this->withToken($this->token)->getJson('/api/entregas/resumo')
        ->assertOk()
        ->assertJsonPath('atribuidas', 2)
        ->assertJsonPath('em_rota', 1)
        ->assertJsonPath('prontas_para_retirada', 1)
        ->assertJsonPath('entregues_hoje', 0);
});

it('confirma a entrega com o PIN correto', function () {
    $pedido = criarPedidoDeEntrega($this->loja, $this->cliente, $this->entregador);

    $this->withToken($this->token)
        ->postJson("/api/entregas/{$pedido->id}/confirmar", ['pin' => '123456'])
        ->assertOk()
        ->assertJsonPath('status', 'entregue');

    expect($pedido->refresh()->pin_validado_em)->not->toBeNull();
});

it('persiste as tentativas de PIN incorreto e bloqueia na terceira', function () {
    $pedido = criarPedidoDeEntrega($this->loja, $this->cliente, $this->entregador);

    $this->withToken($this->token)
        ->postJson("/api/entregas/{$pedido->id}/confirmar", ['pin' => '000000'])
        ->assertStatus(422)
        ->assertJsonPath('message', 'PIN incorreto. 2 tentativa(s) restante(s).');

    expect($pedido->refresh()->pin_tentativas)->toBe(1);

    $this->withToken($this->token)->postJson("/api/entregas/{$pedido->id}/confirmar", ['pin' => '000001']);
    $this->withToken($this->token)->postJson("/api/entregas/{$pedido->id}/confirmar", ['pin' => '000002']);

    $this->withToken($this->token)
        ->postJson("/api/entregas/{$pedido->id}/confirmar", ['pin' => '123456'])
        ->assertStatus(429);

    expect($pedido->refresh()->status->value)->toBe('em_rota');
});

it('não confirma pedido que ainda não saiu para entrega', function () {
    $pedido = criarPedidoDeEntrega($this->loja, $this->cliente, $this->entregador, 'pronto');

    $this->withToken($this->token)
        ->postJson("/api/entregas/{$pedido->id}/confirmar", ['pin' => '123456'])
        ->assertStatus(422);
});

it('não confirma pedido de outro entregador', function () {
    $outroEntregador = criarUsuario();
    $pedido = criarPedidoDeEntrega($this->loja, $this->cliente, $outroEntregador);

    $this->withToken($this->token)
        ->postJson("/api/entregas/{$pedido->id}/confirmar", ['pin' => '123456'])
        ->assertNotFound();
});

it('valida o formato do PIN', function () {
    $pedido = criarPedidoDeEntrega($this->loja, $this->cliente, $this->entregador);

    $this->withToken($this->token)
        ->postJson("/api/entregas/{$pedido->id}/confirmar", ['pin' => '12'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('pin');
});
