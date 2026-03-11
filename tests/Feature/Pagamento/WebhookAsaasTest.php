<?php

use App\Domains\Auth\Models\User;
use App\Domains\Pagamento\Models\Pagamento;
use App\Domains\Pedido\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
