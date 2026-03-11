<?php

namespace App\Domains\Pedido\Controllers;

use App\Domains\Pedido\Services\DeliveryFeeService;
use App\Domains\Pedido\Services\PedidoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicPedidoController extends Controller
{
    public function __construct(private readonly PedidoService $pedidoService) {}

    /**
     * POST /api/public/pedidos — Create order from consumer cart.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'loja_id' => 'required|ulid|exists:lojas,id',
            'endereco_id' => 'required|ulid|exists:enderecos,id',
            'metodo_pagamento' => 'required|string|in:pix,cartao,dinheiro',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|ulid|exists:produtos,id',
            'itens.*.quantidade_solicitada' => 'required|integer|min:1',
        ]);

        // Verify the address belongs to the authenticated user
        $endereco = \App\Domains\Endereco\Models\Endereco::where('user_id', auth()->id())
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

        try {
            $pedido = $this->pedidoService->store($orderData);

            return response()->json([
                'id' => $pedido->id,
                'status' => $pedido->status,
                'subtotal' => $pedido->subtotal,
                'taxa_entrega' => $pedido->taxa_entrega,
                'total' => $pedido->total,
            ], 201);
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
            $endereco = \App\Domains\Endereco\Models\Endereco::where('user_id', auth()->id())
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
        $pedidos = \App\Domains\Pedido\Models\Pedido::with(['loja', 'itemPedidos.produto'])
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json($pedidos);
    }

    /**
     * GET /api/public/pedidos/{id} — Show a single order (consumer must own it).
     */
    public function show(string $id): JsonResponse
    {
        $pedido = \App\Domains\Pedido\Models\Pedido::with(['loja', 'itemPedidos.produto', 'endereco'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json($pedido);
    }
}
