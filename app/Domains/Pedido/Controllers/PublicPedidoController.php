<?php

namespace App\Domains\Pedido\Controllers;

use App\Domains\Endereco\Models\Endereco;
use App\Domains\Pagamento\Services\PagamentoService;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Pedido\Services\DeliveryFeeService;
use App\Domains\Pedido\Services\PedidoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicPedidoController extends Controller
{
    public function __construct(
        private readonly PedidoService $pedidoService,
        private readonly PagamentoService $pagamentoService,
    ) {}

    /**
     * POST /api/public/pedidos — Create order from consumer cart.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'loja_id' => 'required|ulid|exists:lojas,id',
            'endereco_id' => 'required|ulid|exists:enderecos,id',
            'metodo_pagamento' => 'required|string|in:pix,cartao,dinheiro,cartao_online',
            'cpf' => 'nullable|string|max:14',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|ulid|exists:produtos,id',
            'itens.*.quantidade_solicitada' => 'required|integer|min:1',
            'cartao' => 'required_if:metodo_pagamento,cartao_online|array',
            'cartao.holderName' => 'required_if:metodo_pagamento,cartao_online|string|max:100',
            'cartao.number' => 'required_if:metodo_pagamento,cartao_online|string|min:13|max:16',
            'cartao.expiryMonth' => 'required_if:metodo_pagamento,cartao_online|string|size:2',
            'cartao.expiryYear' => 'required_if:metodo_pagamento,cartao_online|string|size:4',
            'cartao.ccv' => 'required_if:metodo_pagamento,cartao_online|string|min:3|max:4',
        ]);

        // Verify the address belongs to the authenticated user
        $endereco = Endereco::where('user_id', auth()->id())
            ->findOrFail($data['endereco_id']);

        // Calculate delivery fee
        $taxaEntrega = DeliveryFeeService::calculate($data['loja_id'], $endereco);

        $orderData = [
            'loja_id' => $data['loja_id'],
            'endereco_id' => $data['endereco_id'],
            'user_id' => auth()->id(),
            'status' => 'aguardando_pagamento',
            'taxa_entrega' => $taxaEntrega,
            'itens' => $data['itens'],
        ];

        $user = auth()->user();
        if (isset($data['cpf']) && ! $user->cpf) {
            $user->update(['cpf' => $data['cpf']]);
        }

        if (! $user->cpf) {
            return response()->json(['message' => 'O CPF é obrigatório para processar o pagamento.'], 422);
        }

        try {
            $pedido = $this->pedidoService->store($orderData);

            // Create charge on Asaas
            $metodo = $data['metodo_pagamento'];
            $paymentData = $data['cartao'] ?? [];
            if ($metodo === 'cartao_online') {
                $paymentData['remoteIp'] = $request->ip();
            }

            $result = $this->pagamentoService->criarCobranca($pedido, $metodo, $paymentData);

            $response = [
                'id' => $pedido->id,
                'status' => $pedido->status,
                'subtotal' => $pedido->subtotal,
                'taxa_entrega' => $pedido->taxa_entrega,
                'total' => $pedido->total,
                'pagamento' => [
                    'id' => $result['pagamento']->id,
                    'status' => $result['pagamento']->status,
                    'metodo' => $result['pagamento']->metodo,
                    'asaas_charge_id' => $result['pagamento']->asaas_charge_id,
                ],
            ];

            // Add PIX data if available
            if ($metodo === 'pix' && $result['pix']) {
                $response['pagamento']['pix'] = [
                    'qr_code' => $result['pix']['encodedImage'] ?? null,
                    'copy_paste' => $result['pix']['payload'] ?? null,
                    'expiration_date' => $result['pix']['expirationDate'] ?? null,
                ];
            }

            // Add invoice URL for card/boleto
            if (isset($result['charge']['invoiceUrl'])) {
                $response['pagamento']['invoice_url'] = $result['charge']['invoiceUrl'];
            }

            return response()->json($response, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * GET /api/public/pedidos/preview — Estimate delivery fee before placing order.
     */
    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'loja_id' => 'required|ulid|exists:lojas,id',
            'endereco_id' => 'nullable|ulid|exists:enderecos,id',
        ]);

        $taxaEntrega = 0;

        if ($request->endereco_id) {
            $endereco = Endereco::where('user_id', auth()->id())
                ->find($request->endereco_id);

            if ($endereco) {
                $taxaEntrega = DeliveryFeeService::calculate($request->loja_id, $endereco);
            }
        }

        return response()->json([
            'taxa_entrega' => $taxaEntrega,
        ]);
    }

    /**
     * GET /api/public/pedidos/meus — List consumer's own orders.
     */
    public function meusPedidos(): JsonResponse
    {
        $pedidos = Pedido::with(['loja', 'itemPedidos.produto', 'pagamento'])
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json($pedidos);
    }

    /**
     * GET /api/public/pedidos/{id} — Show a single order (consumer must own it).
     * Includes delivery PIN when order status is 'em_rota'.
     */
    public function show(string $id): JsonResponse
    {
        $pedido = Pedido::with(['loja', 'itemPedidos.produto', 'endereco', 'pagamento'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $data = $pedido->toArray();

        // Only expose PIN when order is in delivery
        if ($pedido->status === 'em_rota' && ! $pedido->pin_validado_em) {
            $data['pin_entrega'] = $pedido->pin_entrega;
        } else {
            unset($data['pin_entrega']);
        }
        unset($data['pin_tentativas']);

        return response()->json($data);
    }

    /**
     * GET /api/public/pedidos/{id}/pagamento/status — Check payment status (for polling).
     */
    public function paymentStatus(string $id): JsonResponse
    {
        $pedido = Pedido::with('pagamento')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        if (! $pedido->pagamento) {
            return response()->json(['message' => 'Pagamento não encontrado'], 404);
        }

        // Sync status from Asaas
        $pagamento = $this->pagamentoService->sincronizarStatus($pedido->pagamento);

        return response()->json([
            'status' => $pagamento->status,
            'metodo' => $pagamento->metodo,
            'pago_em' => $pagamento->pago_em,
        ]);
    }
}
