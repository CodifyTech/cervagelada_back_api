<?php

namespace App\Domains\Relatorios\Controllers;

use App\Domains\Relatorios\Services\RelatoriosService;
use App\Domains\Shared\Controller\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RelatoriosController extends BaseController
{
    public function __construct(private readonly RelatoriosService $service)
    {
        $this->setACL('relatorios', [
            'list' => ['relatorios.read'],
        ]);
        parent::__construct();
        $this->setService($this->service);
    }

    /**
     * GET /api/relatorios/pedidos
     */
    public function pedidos(Request $request): JsonResponse|StreamedResponse
    {
        $filtros = $request->only(['de', 'ate', 'cidade', 'status', 'loja_id', 'per_page', 'page']);

        if ($request->get('formato') === 'csv') {
            return $this->service->exportarCsv('pedidos', $filtros);
        }

        return response()->json($this->service->getPedidos($filtros));
    }

    /**
     * GET /api/relatorios/produtos-mais-vendidos
     */
    public function produtosMaisVendidos(Request $request): JsonResponse|StreamedResponse
    {
        $filtros = $request->only(['de', 'ate', 'limite']);

        if ($request->get('formato') === 'csv') {
            return $this->service->exportarCsv('produtos', $filtros);
        }

        return response()->json($this->service->getProdutosMaisVendidos($filtros));
    }

    /**
     * GET /api/relatorios/sellers
     */
    public function sellers(Request $request): JsonResponse
    {
        $filtros = $request->only(['de', 'ate', 'regiao', 'per_page', 'page']);

        return response()->json($this->service->getSellers($filtros));
    }

    /**
     * GET /api/relatorios/financeiro
     */
    public function financeiro(Request $request): JsonResponse|StreamedResponse
    {
        $filtros = $request->only(['de', 'ate', 'per_page', 'page']);

        if ($request->get('formato') === 'csv') {
            return $this->service->exportarCsv('financeiro', $filtros);
        }

        return response()->json($this->service->getFinanceiro($filtros));
    }
}
