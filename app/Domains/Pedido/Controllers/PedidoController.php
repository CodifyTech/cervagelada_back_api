<?php

namespace App\Domains\Pedido\Controllers;

use App\Domains\Pedido\Enums\OrderStatus;
use App\Domains\Pedido\Requests\PedidoRequest;
use App\Domains\Pedido\Services\PedidoService;
use App\Domains\Shared\Controller\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PedidoController extends BaseController
{
    public function __construct(private readonly PedidoService $service)
    {
        $this->setACL('pedido', [
            'list' => ['pedido.index'],
            'create' => ['pedido.store'],
            'edit' => ['pedido.update'],
            'delete' => ['pedido.destroy'],
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', PedidoRequest::class);
    }

    /**
     * Returns paginated orders for the authenticated user's store.
     * Accepts optional query params: status, data_inicio, data_fim, search, per_page.
     */
    public function listarLoja(Request $request): JsonResponse
    {
        return response()->json($this->service->listarLoja($request->all()));
    }

    /**
     * Returns order status summary counts for the authenticated user's store.
     * Used to populate the dashboard stat cards (pending, completed, etc.).
     */
    public function resumoLoja(): JsonResponse
    {
        return response()->json($this->service->resumoLoja());
    }

    /**
     * Updates only the status of a specific order. Intended for the store
     * manager to advance an order through the workflow.
     */
    public function atualizarStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:'.OrderStatus::valuesString()],
        ]);

        $targetStatus = OrderStatus::from($request->input('status'));

        // Block transition to 'entregue' without PIN validation
        if ($targetStatus === OrderStatus::ENTREGUE) {
            return response()->json([
                'message' => 'Use o endpoint de validação de PIN para concluir a entrega.',
            ], 422);
        }

        // Block manual transition from aguardando_pagamento (only webhook can advance)
        $pedido = $this->service->findById($id);
        if ($pedido->status === OrderStatus::AGUARDANDO_PAGAMENTO) {
            return response()->json([
                'message' => 'Pedido aguardando pagamento. O status será atualizado automaticamente após confirmação.',
            ], 422);
        }

        try {
            return response()->json($this->service->update($request->only('status'), $id));
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 422;

            return response()->json(['message' => $e->getMessage()], $code);
        }
    }

    /**
     * POST /api/pedidos/{id}/validar-pin — Validate delivery PIN and mark as delivered.
     */
    public function validarPin(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'pin' => ['required', 'string', 'size:6'],
        ]);

        try {
            $pedido = $this->service->validarPin($id, $request->input('pin'));

            return response()->json($pedido);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 422;

            return response()->json(['message' => $e->getMessage()], $code);
        }
    }
}
