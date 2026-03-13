<?php

namespace App\Domains\Pagamento\Controllers;

use App\Domains\Auditoria\Services\AuditService;
use App\Domains\Pagamento\Models\Pagamento;
use App\Domains\Pagamento\Services\PagamentoService;
use App\Domains\Pedido\Enums\OrderStatus;
use App\Domains\Pedido\Events\NewOrderReceived;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AsaasWebhookController extends Controller
{
    public function __construct(
        private readonly PagamentoService $pagamentoService,
        private readonly AuditService $auditService = new AuditService,
    ) {}

    /**
     * POST /api/webhooks/asaas — Handle Asaas payment webhook callbacks.
     */
    public function handle(Request $request): JsonResponse
    {
        // Validate webhook token if configured
        $expectedToken = config('services.asaas.webhook_token');
        if ($expectedToken && $request->header('asaas-access-token') !== $expectedToken) {
            Log::warning('Asaas webhook: invalid token', [
                'ip' => $request->ip(),
            ]);
            $this->auditService->log('webhook_asaas_invalid_token', 'webhook', null, null, null, [
                'ip' => $request->ip(),
            ]);

            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $event = $request->input('event');
        $payment = $request->input('payment', []);
        $chargeId = $payment['id'] ?? null;

        Log::info('Asaas webhook received', [
            'event' => $event,
            'charge_id' => $chargeId,
        ]);

        if (! $chargeId) {
            return response()->json(['message' => 'No charge ID'], 200);
        }

        $pagamento = Pagamento::where('asaas_charge_id', $chargeId)->first();

        if (! $pagamento) {
            Log::warning('Asaas webhook: pagamento not found', ['charge_id' => $chargeId]);

            return response()->json(['message' => 'Not found'], 200);
        }

        // Store raw webhook payload for audit
        $pagamento->update([
            'webhook_payload' => $request->all(),
        ]);

        $newStatus = $this->pagamentoService->mapAsaasStatus($payment['status'] ?? '');

        $this->processStatusChange($pagamento, $newStatus);

        return response()->json(['message' => 'OK'], 200);
    }

    /**
     * Process payment status change and trigger side effects.
     */
    private function processStatusChange(Pagamento $pagamento, string $newStatus): void
    {
        $oldStatus = $pagamento->status;

        if ($oldStatus === $newStatus) {
            return;
        }

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'pago') {
            $updateData['pago_em'] = now();
        }

        $pagamento->update($updateData);

        $pedido = $pagamento->pedido;

        match ($newStatus) {
            'pago' => $this->onPaymentConfirmed($pedido),
            'recusado', 'cancelado' => $this->onPaymentFailed($pedido),
            'estornado' => $this->onPaymentRefunded($pedido),
            default => null,
        };

        $auditAction = match ($newStatus) {
            'pago' => 'payment_received',
            'recusado' => 'payment_recusado',
            'cancelado' => 'payment_cancelado',
            'estornado' => 'payment_estornado',
            default => "payment_{$newStatus}",
        };

        $this->auditService->logPaymentEvent($auditAction, $pedido->id, [
            'charge_id' => $pagamento->asaas_charge_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);

        Log::info('Asaas webhook processed', [
            'charge_id' => $pagamento->asaas_charge_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'pedido_id' => $pedido->id,
        ]);
    }

    /**
     * Handle confirmed payment: advance order to 'pendente' (awaiting seller).
     */
    private function onPaymentConfirmed($pedido): void
    {
        if ($pedido->status === OrderStatus::AGUARDANDO_PAGAMENTO) {
            $pedido->update(['status' => OrderStatus::RECEBIDO->value]);

            // Notify seller about the new paid order
            NewOrderReceived::dispatch($pedido->load(['itemPedidos', 'loja', 'user']));
        }
    }

    /**
     * Handle failed/cancelled payment: cancel order and restore stock.
     */
    private function onPaymentFailed($pedido): void
    {
        if ($pedido->status === OrderStatus::AGUARDANDO_PAGAMENTO) {
            // Restore stock
            foreach ($pedido->itemPedidos as $item) {
                \DB::table('loja_produtos')
                    ->where('loja_id', $pedido->loja_id)
                    ->where('produto_id', $item->produto_id)
                    ->increment('estoque', $item->quantidade_final);
            }

            $pedido->update(['status' => OrderStatus::CANCELADO->value]);
        }
    }

    /**
     * Handle refund: mark order as cancelled.
     */
    private function onPaymentRefunded($pedido): void
    {
        if (! in_array($pedido->status, [OrderStatus::CANCELADO, OrderStatus::ENTREGUE])) {
            $pedido->update(['status' => OrderStatus::CANCELADO->value]);
        }
    }
}
