<?php

namespace App\Domains\Pedido\Controllers;

use App\Domains\Shared\Controller\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Domains\Pedido\Services\PedidoService;
use App\Domains\Pedido\Requests\PedidoRequest;

class PedidoController extends BaseController
{
    public function __construct(private readonly PedidoService $service)
    {
        $this->setACL('pedido', [
            'list' => ['pedido.index'],
            'create' => ['pedido.store'],
            'edit'=> ['pedido.update'],
            'delete' => ['pedido.destroy']
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
            'status' => ['required', 'in:pendente,preparando,pronto,em_rota,entregue,cancelado'],
        ]);

        return response()->json($this->service->update($request->only('status'), $id));
    }
}
