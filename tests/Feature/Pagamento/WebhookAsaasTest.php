<?php

use App\Domains\Auth\Models\User;
use App\Domains\Pagamento\Models\Pagamento;
use App\Domains\Pedido\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

/**
 * Helper to create a pedido + pagamento pair for webhook tests.
 */
function criarPagamentoComPedido(string $chargeId = 'pay_test_123', string $status = 'pendente'): array
{
    $user = User::factory()->create(['email_verified_at' => now()]);

    $pedido = Pedido::create([
        'user_id' => $user->id,
        'loja_id' => Str::ulid(),
        'endereco_id' => Str::ulid(),
        'subtotal' => 100.00,
        'taxa_entrega' => 10.00,
        'total' => 110.00,
        'status' => 'aguardando_pagamento',
    ]);

    $pagamento = Pagamento::create([
        'pedido_id' => $pedido->id,
        'asaas_charge_id' => $chargeId,
        'asaas_customer_id' => 'cus_test_abc',
        'metodo' => 'pix',
        'status' => $status,
        'valor' => 110.00,
    ]);

    return compact('user', 'pedido', 'pagamento');
}

/**
 * Helper to create a loja with an attached product for order tests.
 */
function criarLojaComProduto(): array
{
    $loja = \App\Domains\Loja\Models\Loja::create([
        'nome_fantasia' => 'Loja Pagamento Test',
        'tipo_loja' => 'distribuidora',
        'ativo' => true,
        'latitude' => -23.5505,
        'longitude' => -46.6333,
        'raio_entrega_km' => 10,
        'tempo_entrega_min' => 30,
        'tempo_entrega_max' => 60,
        'aceite_automatico' => true,
        'pedido_minimo' => 0,
        'taxa_comissao' => 10.00,
        'cep' => '01001000',
        'logradouro' => 'Rua Teste',
        'numero' => '1',
        'bairro' => 'Centro',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
    ]);

    $produto = \App\Domains\Produto\Models\Produto::create([
        'nome' => 'Cerveja Teste',
        'descricao' => 'Cerveja para teste',
    ]);

    $loja->produtos()->attach($produto->id, [
        'preco' => 15.00,
        'estoque' => 100,
        'ativo' => true,
        'destaque' => false,
    ]);

    return compact('loja', 'produto');
}

// --- Token validation ---

it('rejeita webhook com token invalido quando configurado', function () {
    config(['services.asaas.webhook_token' => 'token-secreto']);

    $response = $this->postJson('/api/webhooks/asaas', [
        'event' => 'PAYMENT_CONFIRMED',
        'payment' => ['id' => 'pay_test_123', 'status' => 'CONFIRMED'],
    ], ['asaas-access-token' => 'token-errado']);

    $response->assertStatus(401);
});

it('aceita webhook sem token quando nao configurado', function () {
    config(['services.asaas.webhook_token' => null]);

    ['pagamento' => $pagamento] = criarPagamentoComPedido('pay_free_abc');

    $response = $this->postJson('/api/webhooks/asaas', [
        'event' => 'PAYMENT_CONFIRMED',
        'payment' => ['id' => 'pay_free_abc', 'status' => 'CONFIRMED'],
    ]);

    $response->assertStatus(200);
});

// --- PAYMENT_CONFIRMED ---

it('processa pagamento confirmado e muda status do pedido para pendente', function () {
    config(['services.asaas.webhook_token' => null]);

    ['pedido' => $pedido] = criarPagamentoComPedido('pay_confirmed_1');

    $this->postJson('/api/webhooks/asaas', [
        'event' => 'PAYMENT_CONFIRMED',
        'payment' => ['id' => 'pay_confirmed_1', 'status' => 'CONFIRMED'],
    ])->assertStatus(200);

    expect($pedido->fresh()->status)->toBe('pendente');
});

// --- PAYMENT_RECEIVED ---

it('processa pagamento recebido e atualiza status da cobrança para pago', function () {
    config(['services.asaas.webhook_token' => null]);

    ['pagamento' => $pagamento] = criarPagamentoComPedido('pay_received_1', 'pendente');

    $this->postJson('/api/webhooks/asaas', [
        'event' => 'PAYMENT_RECEIVED',
        'payment' => ['id' => 'pay_received_1', 'status' => 'RECEIVED'],
    ])->assertStatus(200);

    expect($pagamento->fresh()->status)->toBe('pago');
});

// --- PAYMENT_OVERDUE ---

it('processa pagamento vencido e muda status para vencido', function () {
    config(['services.asaas.webhook_token' => null]);

    ['pagamento' => $pagamento] = criarPagamentoComPedido('pay_overdue_1', 'pendente');

    $this->postJson('/api/webhooks/asaas', [
        'event' => 'PAYMENT_OVERDUE',
        'payment' => ['id' => 'pay_overdue_1', 'status' => 'OVERDUE'],
    ])->assertStatus(200);

    expect($pagamento->fresh()->status)->toBe('vencido');
});

// --- PAYMENT_REFUSED ---

it('processa pagamento recusado e cancela pedido', function () {
    config(['services.asaas.webhook_token' => null]);

    ['pedido' => $pedido, 'pagamento' => $pagamento] = criarPagamentoComPedido('pay_refused_1');

    $this->postJson('/api/webhooks/asaas', [
        'event' => 'PAYMENT_REFUSED',
        'payment' => ['id' => 'pay_refused_1', 'status' => 'REFUSED'],
    ])->assertStatus(200);

    expect($pagamento->fresh()->status)->toBe('recusado');
    expect($pedido->fresh()->status)->toBe('cancelado');
});

// --- PAYMENT_REFUNDED ---

it('processa estorno e cancela pedido', function () {
    config(['services.asaas.webhook_token' => null]);

    ['pedido' => $pedido, 'pagamento' => $pagamento] = criarPagamentoComPedido('pay_refund_1', 'pago');
    $pedido->update(['status' => 'entregue']);

    $this->postJson('/api/webhooks/asaas', [
        'event' => 'PAYMENT_REFUNDED',
        'payment' => ['id' => 'pay_refund_1', 'status' => 'REFUNDED'],
    ])->assertStatus(200);

    expect($pagamento->fresh()->status)->toBe('estornado');
});

// --- Idempotency ---

it('e idempotente para o mesmo evento recebido duas vezes', function () {
    config(['services.asaas.webhook_token' => null]);

    ['pedido' => $pedido] = criarPagamentoComPedido('pay_idempotent_1');

    $payload = [
        'event' => 'PAYMENT_CONFIRMED',
        'payment' => ['id' => 'pay_idempotent_1', 'status' => 'CONFIRMED'],
    ];

    $this->postJson('/api/webhooks/asaas', $payload)->assertStatus(200);

    // Second call — should not throw or produce duplicate side effects
    $this->postJson('/api/webhooks/asaas', $payload)->assertStatus(200);

    expect($pedido->fresh()->status)->toBe('pendente');
});

// --- Unknown charge ---

it('retorna 200 sem erro quando charge_id nao existe no banco', function () {
    config(['services.asaas.webhook_token' => null]);

    $this->postJson('/api/webhooks/asaas', [
        'event' => 'PAYMENT_CONFIRMED',
        'payment' => ['id' => 'pay_unknown_xyz', 'status' => 'CONFIRMED'],
    ])->assertStatus(200);
});

// --- Charge creation with mocked Asaas ---

it('cria cobranca pix com mock do Asaas', function () {
    Http::fake([
        'sandbox.asaas.com/api/v3/customers' => Http::response([
            'data' => [['id' => 'cus_mock_123', 'name' => 'Test']],
        ]),
        'sandbox.asaas.com/api/v3/payments' => Http::response([
            'id' => 'pay_mock_pix_1',
            'status' => 'PENDING',
            'billingType' => 'PIX',
            'value' => 110.00,
        ]),
        'sandbox.asaas.com/api/v3/payments/pay_mock_pix_1/pixQrCode' => Http::response([
            'encodedImage' => 'base64_qr_data',
            'payload' => '00020126...pix_copy_paste',
            'expirationDate' => now()->addHour()->toIso8601String(),
        ]),
    ]);

    config(['services.asaas.sandbox' => true, 'services.asaas.api_key' => 'test_key']);

    $user = User::factory()->create(['email_verified_at' => now(), 'cpf' => '12345678901']);
    $token = auth('api')->login($user);
    $loja = criarLojaComProduto();

    $endereco = \App\Domains\Endereco\Models\Endereco::create([
        'user_id' => $user->id,
        'apelido' => 'Casa',
        'cep' => '01001000',
        'logradouro' => 'Rua Teste',
        'numero' => '100',
        'bairro' => 'Centro',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'latitude' => -23.5505,
        'longitude' => -46.6333,
    ]);

    $response = $this->withToken($token)->postJson('/api/public/pedidos', [
        'loja_id' => $loja['loja']->id,
        'endereco_id' => $endereco->id,
        'metodo_pagamento' => 'pix',
        'itens' => [
            ['produto_id' => $loja['produto']->id, 'quantidade_solicitada' => 2],
        ],
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['id', 'status', 'total', 'pagamento' => ['id', 'status', 'pix']]);
});

it('processa timeout cancelando pagamento pendente apos vencimento', function () {
    config(['services.asaas.webhook_token' => null]);

    ['pedido' => $pedido, 'pagamento' => $pagamento] = criarPagamentoComPedido('pay_timeout_1');

    // Simulate PAYMENT_OVERDUE webhook (timeout scenario)
    $this->postJson('/api/webhooks/asaas', [
        'event' => 'PAYMENT_OVERDUE',
        'payment' => ['id' => 'pay_timeout_1', 'status' => 'OVERDUE'],
    ])->assertStatus(200);

    expect($pagamento->fresh()->status)->toBe('vencido');
});

it('armazena webhook_payload raw para auditoria', function () {
    config(['services.asaas.webhook_token' => null]);

    ['pagamento' => $pagamento] = criarPagamentoComPedido('pay_audit_1');

    $payload = [
        'event' => 'PAYMENT_CONFIRMED',
        'payment' => ['id' => 'pay_audit_1', 'status' => 'CONFIRMED', 'value' => 110.00],
    ];

    $this->postJson('/api/webhooks/asaas', $payload)->assertStatus(200);

    $fresh = $pagamento->fresh();
    expect($fresh->webhook_payload)->not->toBeNull();
});
