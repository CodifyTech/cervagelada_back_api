<?php

namespace App\Domains\Pagamento\Services;

use App\Domains\Pagamento\Models\Pagamento;
use App\Domains\Pedido\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PagamentoService
{
    public function __construct(
        private readonly AsaasService $asaasService,
    ) {}

    /**
     * Create a charge on Asaas for the given order and persist Pagamento record.
     *
     * @return array{pagamento: Pagamento, charge: array}
     */
    public function criarCobranca(Pedido $pedido, string $metodo, array $paymentData = []): array
    {
        return DB::transaction(function () use ($pedido, $metodo, $paymentData) {
            $user = $pedido->user;
            $pedido->loadMissing('endereco');

            // Resolve or create customer in Asaas
            $customerId = $this->resolveAsaasCustomer($user);

            $billingType = match ($metodo) {
                'pix' => 'PIX',
                'cartao' => 'CREDIT_CARD',
                'cartao_online' => 'CREDIT_CARD',
                'boleto' => 'BOLETO',
                default => 'PIX',
            };

            $chargeData = [
                'customer' => $customerId,
                'billingType' => $billingType,
                'value' => (float) $pedido->total,
                'dueDate' => now()->addDay()->format('Y-m-d'),
                'description' => "Pedido #{$pedido->id} - Cerva Gelada",
                'externalReference' => $pedido->id,
            ];

            if ($metodo === 'cartao_online') {
                $chargeData['creditCard'] = [
                    'holderName' => $paymentData['holderName'],
                    'number' => $paymentData['number'],
                    'expiryMonth' => $paymentData['expiryMonth'],
                    'expiryYear' => $paymentData['expiryYear'],
                    'ccv' => $paymentData['ccv'],
                ];
                
                $chargeData['creditCardHolderInfo'] = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'cpfCnpj' => $user->cpf,
                    'postalCode' => preg_replace('/\D/', '', $pedido->endereco->cep),
                    'addressNumber' => $pedido->endereco->numero ?? 'S/N',
                    'addressComplement' => $pedido->endereco->complemento ?? '',
                    'phone' => preg_replace('/\D/', '', $user->phone ?? ''),
                    'mobilePhone' => preg_replace('/\D/', '', $user->phone ?? ''),
                ];

                if (!empty($paymentData['remoteIp'])) {
                    $chargeData['remoteIp'] = $paymentData['remoteIp'];
                }
            }

            $charge = $this->asaasService->createCharge($chargeData);

            $pagamento = Pagamento::create([
                'pedido_id' => $pedido->id,
                'loja_id' => $pedido->loja_id,
                'asaas_charge_id' => $charge['id'],
                'asaas_customer_id' => $customerId,
                'metodo' => $metodo,
                'status' => 'pendente',
                'valor' => $pedido->total,
            ]);

            // If PIX, fetch QR code
            $pixData = null;
            if ($metodo === 'pix') {
                try {
                    $pixData = $this->asaasService->getPixQrCode($charge['id']);
                } catch (\Throwable $e) {
                    Log::warning('Could not fetch PIX QR code', ['error' => $e->getMessage()]);
                }
            }

            return [
                'pagamento' => $pagamento,
                'charge' => $charge,
                'pix' => $pixData,
            ];
        });
    }

    /**
     * Check payment status on Asaas and sync local record.
     */
    public function sincronizarStatus(Pagamento $pagamento): Pagamento
    {
        $charge = $this->asaasService->getCharge($pagamento->asaas_charge_id);

        $newStatus = $this->mapAsaasStatus($charge['status']);
        if ($newStatus !== $pagamento->status) {
            $pagamento->update([
                'status' => $newStatus,
                'pago_em' => $newStatus === 'pago' ? now() : $pagamento->pago_em,
            ]);
        }

        return $pagamento->refresh();
    }

    /**
     * Map Asaas status to local status.
     */
    public function mapAsaasStatus(string $asaasStatus): string
    {
        return match ($asaasStatus) {
            'CONFIRMED', 'RECEIVED', 'RECEIVED_IN_CASH' => 'pago',
            'PENDING', 'AWAITING_RISK_ANALYSIS' => 'pendente',
            'OVERDUE', 'REFUND_REQUESTED', 'CHARGEBACK_REQUESTED', 'CHARGEBACK_DISPUTE',
            'AWAITING_CHARGEBACK_REVERSAL', 'DUNNING_REQUESTED', 'DUNNING_RECEIVED' => 'pendente',
            'REFUNDED' => 'estornado',
            'CANCELLED' => 'cancelado',
            'DECLINED' => 'recusado',
            default => 'pendente',
        };
    }

    /**
     * Resolve Asaas customer ID: find existing or create new.
     */
    private function resolveAsaasCustomer($user): string
    {
        // Try to find by CPF first
        if ($user->cpf) {
            $existing = $this->asaasService->findCustomerByCpfCnpj($user->cpf);
            if ($existing) {
                return $existing['id'];
            }
        }

        $customer = $this->asaasService->createCustomer([
            'name' => $user->name,
            'cpfCnpj' => $user->cpf ?? '',
            'email' => $user->email,
            'phone' => $user->phone ?? '',
        ]);

        return $customer['id'];
    }
}
