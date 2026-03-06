<?php

namespace App\Domains\Pedido\Services;

use App\Domains\Pedido\Models\Pedido;
use App\Domains\Shared\Services\BaseService;
use App\Domains\Shared\Services\AsaasService;
use Illuminate\Support\Str;

class PedidoService extends BaseService
{
    public function __construct(
        private readonly Pedido $pedido,
        private readonly AsaasService $asaasService
    ) {
        $this->setModel($this->pedido);
    }

    /**
     * Stores a new Pedido with its items.
     *
     * @param  array  $data
     * @return Pedido
     * @throws \Exception
     */
    public function store(array $data)
    {
        return \DB::transaction(function () use ($data) {
            $itens = $data['itens'] ?? [];
            unset($data['itens']);

            $lojaId = $data['loja_id'];
            $user = auth()->user();

            // 1. Sync Customer with Asaas
            if ($user) {
                if (empty($user->asaas_customer_id)) {
                    $customerData = [
                        'name' => $user->name,
                        'email' => $user->email,
                        'cpfCnpj' => $data['cpf'] ?? $user->cpf, // Prefer data from request
                        'mobilePhone' => $data['telefone'] ?? $user->telefone,
                    ];

                    // Basic validation for creating customer
                    if (empty($customerData['cpfCnpj']) || empty($customerData['mobilePhone'])) {
                         throw new \Exception("CPF e Telefone são obrigatórios para gerar o pagamento.");
                    }

                    $asaasCustomer = $this->asaasService->criarCliente($customerData);

                    if (isset($asaasCustomer['id'])) {
                        $user->update([
                            'asaas_customer_id' => $asaasCustomer['id'],
                            'cpf' => $customerData['cpfCnpj'],
                            'telefone' => $customerData['mobilePhone']
                        ]);
                    } else {
                        throw new \Exception("Erro ao criar cliente no Asaas: " . json_encode($asaasCustomer));
                    }
                }
                $data['user_id'] = $user->id;
            }

            // Fallback status
            if (!isset($data['status'])) {
                $data['status'] = 'pendente';
            }

            // Calculation and Stock Validation
            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($itens as $item) {
                $produtoId = $item['produto_id'];
                $quantidade = $item['quantidade_solicitada'];

                // Check stock in the store
                $lojaProduto = \DB::table('loja_produtos')
                    ->where('loja_id', $lojaId)
                    ->where('produto_id', $produtoId)
                    ->first();

                if (!$lojaProduto) {
                    throw new \Exception("O produto #{$produtoId} não está disponível nesta loja.");
                }

                if ($lojaProduto->estoque < $quantidade) {
                    throw new \Exception("Estoque insuficiente para o produto #{$produtoId}. Disponível: {$lojaProduto->estoque}");
                }

                // Use the database price for security (check for promotional price)
                $preco = $lojaProduto->preco_promocional ?? $lojaProduto->preco;

                $precoTotal = $preco * $quantidade;
                $subtotal += $precoTotal;

                $itemsToCreate[] = [
                    'id' => Str::ulid(),
                    'produto_id' => $produtoId,
                    'quantidade_solicitada' => $quantidade,
                    'quantidade_final' => $quantidade,
                    'preco_unitario' => $preco, // Use decimal value
                    'preco_total' => $precoTotal,
                    'ajuste_preco' => 0,
                    'observacoes' => $item['observacoes'] ?? null,
                ];

                // Reduce stock
                \DB::table('loja_produtos')
                    ->where('id', $lojaProduto->id)
                    ->decrement('estoque', $quantidade);
            }

            $taxaEntrega = $data['taxa_entrega'] ?? 0;
            $data['subtotal'] = $subtotal;
            $data['taxa_entrega'] = $taxaEntrega;
            $data['total'] = $subtotal + $taxaEntrega;

            /** @var Pedido $pedido */
            $pedido = $this->model->create($data);

            foreach ($itemsToCreate as $itemData) {
                $pedido->itemPedidos()->create($itemData);
            }

            // 2. Create Payment in Asaas
            if (isset($data['forma_pagamento'])) {
                $formaPagamento = $data['forma_pagamento']; // PIX, CREDIT_CARD

                $cobrancaData = [
                    'customer' => $user->asaas_customer_id,
                    'billingType' => $formaPagamento,
                    'value' => $data['total'],
                    'dueDate' => now()->addDays(1)->format('Y-m-d'),
                    'description' => "Pedido #{$pedido->id}",
                    'externalReference' => $pedido->id,
                ];

                if ($formaPagamento === 'CREDIT_CARD') {
                    if (!isset($data['credit_card_token'])) {
                         throw new \Exception("Token do cartão de crédito é obrigatório.");
                    }
                     $cobrancaData['creditCardToken'] = $data['credit_card_token'];
                }

                $cobranca = $this->asaasService->criarCobranca($cobrancaData);

                if (isset($cobranca['id'])) {
                    $transacaoData = [
                        'tipo' => 'credito', // It's a payment IN
                        'valor' => $data['total'],
                        'descricao' => "Pagamento Pedido #{$pedido->id}",
                        'liquidado' => false,
                        'loja_id' => $lojaId,
                        'pedido_id' => $pedido->id,
                        'gateway_id' => $cobranca['id'],
                        'gateway_status' => $cobranca['status'],
                        'forma_pagamento' => $formaPagamento,
                    ];

                    if ($formaPagamento === 'PIX') {
                         $qrCodeData = $this->asaasService->obterQRCode($cobranca['id']);
                         $transacaoData['pix_qr_code'] = $qrCodeData['payload'] ?? null;
                         $transacaoData['pix_qr_code_url'] = $qrCodeData['encodedImage'] ?? null;
                    }

                    $pedido->transacoesFinanceiras()->create($transacaoData);
                } else {
                     // Log error but maybe don't fail the order entirely? Or fail?
                     // For now, let's fail to maintain consistency
                     throw new \Exception("Erro ao criar cobrança no Asaas: " . json_encode($cobranca));
                }
            }

            return $pedido->load(['itemPedidos', 'transacoesFinanceiras']);
        });
    }

    /**
     * Updates an existing Pedido and handles status transitions.
     *
     * @param  array  $data
     * @param  string  $id
     * @return Pedido
     */
    public function update(array $data, string $id)
    {
        return \DB::transaction(function () use ($data, $id) {
            $pedido = $this->findById($id);
            $oldStatus = $pedido->status;
            $newStatus = $data['status'] ?? $oldStatus;

            // If cancelling an order that wasn't cancelled before, return stock
            if ($newStatus === 'cancelado' && $oldStatus !== 'cancelado') {
                foreach ($pedido->itemPedidos as $item) {
                    \DB::table('loja_produtos')
                        ->where('loja_id', $pedido->loja_id)
                        ->where('produto_id', $item->produto_id)
                        ->increment('estoque', $item->quantidade_final);
                }

                // TODO: Refund functionality if payment was made?
            }

            $pedido->update($data);

            return $pedido->refresh();
        });
    }
}
