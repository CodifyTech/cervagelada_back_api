<?php

namespace App\Domains\Shared\Controller;

use Illuminate\Http\Request;
use App\Domains\TransacoesFinanceiras\Models\TransacoesFinanceiras;
use App\Domains\Pedido\Models\Pedido;
use Illuminate\Support\Facades\Log;

class AsaasWebhookController extends BaseController
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        $event = $payload['event'] ?? null;
        $paymentId = $payload['payment']['id'] ?? null;

        Log::info("Asaas Webhook Received: {$event}", ['payment_id' => $paymentId]);

        if (!$event || !$paymentId) {
            return response()->json(['status' => 'ignored'], 200);
        }

        $transacao = TransacoesFinanceiras::where('gateway_id', $paymentId)->first();

        if (!$transacao) {
            Log::warning("Transação não encontrada para o pagamento Asaas: {$paymentId}");
            return response()->json(['status' => 'not_found'], 200); // Return 200 to acknowledge receipt
        }

        switch ($event) {
            case 'PAYMENT_RECEIVED':
                $this->handlePaymentReceived($transacao, $payload['payment']);
                break;
            case 'PAYMENT_REFUNDED':
                $this->handlePaymentRefunded($transacao, $payload['payment']);
                break;
            // Add other cases as needed
        }

        return response()->json(['status' => 'success']);
    }

    private function handlePaymentReceived(TransacoesFinanceiras $transacao, array $paymentData)
    {
        // Update Transaction
        $transacao->update([
            'gateway_status' => 'RECEIVED',
            'liquidado' => true,
            'liquidado_em' => now(), // Or use paymentData['paymentDate'] if consistent format
        ]);

        // Update Pedido
        $pedido = $transacao->pedido;
        if ($pedido && $pedido->status !== 'pago' && $pedido->status !== 'entregue') {
            $pedido->update(['status' => 'pago']);
        }
    }

    private function handlePaymentRefunded(TransacoesFinanceiras $transacao, array $paymentData)
    {
        $transacao->update([
            'gateway_status' => 'REFUNDED',
        ]);

        $pedido = $transacao->pedido;
        if ($pedido) {
             // Logic to cancel order or just mark as disputed?
             // Usually refund implies cancellation or partial refund.
             // For now, let's act conservatively and just update transaction.
        }
    }
}
