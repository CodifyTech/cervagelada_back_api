<?php

namespace App\Domains\Dashboard\Services;

use App\Domains\Dashboard\Models\DashboardModel;
use App\Domains\Shared\Services\BaseService;

class DashboardService extends BaseService
{
    public function __construct(private readonly DashboardModel $dashboardModel)
    {
        $this->setModel($this->dashboardModel);
    }

    /**
     * Get consolidated dashboard metrics.
     */
    public function getMetricas(): array
    {
        return $this->dashboardModel->getMetricas();
    }

    /**
     * Get monthly sales data.
     */
    public function getVendasMensais(int $ano): array
    {
        return $this->dashboardModel->getVendasMensais($ano);
    }

    /**
     * Get periodic orders count.
     */
    public function getPedidosPorMes(int $ano): array
    {
        return $this->dashboardModel->getPedidosPorMes($ano);
    }

    /**
     * Get top selling categories (by brand).
     */
    public function getCategoriasMaisVendidas(): array
    {
        return $this->dashboardModel->getCategoriasMaisVendidas();
    }

    /**
     * Get top selling products.
     */
    public function getTopProdutos(int $limit = 5): array
    {
        return $this->dashboardModel->getTopProdutos($limit);
    }

    /**
     * Get recent orders.
     */
    public function getPedidosRecentes(int $limit = 5): array
    {
        return $this->dashboardModel->getPedidosRecentes($limit);
    }

    /**
     * Get admin-level extended metrics (users, sellers, orders by status, revenue).
     * Does not require a loja_id - aggregates across all lojas.
     */
    public function getMetricasAdmin(): array
    {
        return $this->dashboardModel->getMetricasAdmin();
    }

    /**
     * Get all dashboard data at once.
     */
    public function getDashboard(int $ano, int $limit = 5): array
    {
        return [
            'metricas' => $this->getMetricas(),
            'vendas_mensais' => $this->getVendasMensais($ano),
            'pedidos_por_mes' => $this->getPedidosPorMes($ano),
            'categorias_mais_vendidas' => $this->getCategoriasMaisVendidas(),
            'top_produtos' => $this->getTopProdutos($limit),
            'pedidos_recentes' => $this->getPedidosRecentes($limit),
        ];
    }
}
