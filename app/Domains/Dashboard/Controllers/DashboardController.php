<?php

namespace App\Domains\Dashboard\Controllers;

use App\Domains\Dashboard\Services\DashboardService;
use App\Domains\Shared\Controller\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function __construct(private readonly DashboardService $service)
    {
        // ACL: assuming 'dashboard.read' permission for all metrics
        $this->setACL('dashboard', [
            'list' => ['dashboard.read'],
        ]);
        parent::__construct();
        $this->setService($this->service);
    }

    /**
     * Main KPIs.
     */
    public function metricas(): JsonResponse
    {
        return response()->json($this->service->getMetricas());
    }

    /**
     * Monthly revenue chart data.
     */
    public function vendasMensais(Request $request): JsonResponse
    {
        $ano = $request->get('ano', date('Y'));

        return response()->json($this->service->getVendasMensais($ano));
    }

    /**
     * Periodic orders count data.
     */
    public function pedidosPorMes(Request $request): JsonResponse
    {
        $ano = $request->get('ano', date('Y'));

        return response()->json($this->service->getPedidosPorMes($ano));
    }

    /**
     * Top selling categories based on product brand.
     */
    public function categoriasMaisVendidas(): JsonResponse
    {
        return response()->json($this->service->getCategoriasMaisVendidas());
    }

    /**
     * Top selling products.
     */
    public function topProdutos(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);

        return response()->json($this->service->getTopProdutos($limit));
    }

    /**
     * Recent orders for the dashboard.
     */
    public function pedidosRecentes(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);

        return response()->json($this->service->getPedidosRecentes($limit));
    }

    /**
     * Admin-level extended metrics (users, sellers, orders by status, revenue).
     */
    public function metricasAdmin(): JsonResponse
    {
        return response()->json($this->service->getMetricasAdmin());
    }

    /**
     * Consolidated dashboard data.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $ano = $request->get('ano', date('Y'));
        $limit = $request->get('limit', 5);

        return response()->json($this->service->getDashboard($ano, $limit));
    }
}
