<?php

namespace App\Domains\Dashboard\Services;

use App\Domains\Shared\Services\BaseService;
use App\Domains\Dashboard\Models\DashboardModel;

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
        return $this->dashboardModel->getMetricas($this->resolveLojaId());
    }

    /**
     * Get monthly sales data.
     */
    public function getVendasMensais(int $ano): array
    {
        return $this->dashboardModel->getVendasMensais($this->resolveLojaId(), $ano);
    }

    /**
     * Get periodic orders count.
     */
    public function getPedidosPorMes(int $ano): array
    {
        return $this->dashboardModel->getPedidosPorMes($this->resolveLojaId(), $ano);
    }

    /**
     * Get top selling categories (by brand).
     */
    public function getCategoriasMaisVendidas(): array
    {
        return $this->dashboardModel->getCategoriasMaisVendidas($this->resolveLojaId());
    }

    /**
     * Get top selling products.
     */
    public function getTopProdutos(int $limit = 5): array
    {
        return $this->dashboardModel->getTopProdutos($this->resolveLojaId(), $limit);
    }

    /**
     * Get recent orders.
     */
    public function getPedidosRecentes(int $limit = 5): array
    {
        return $this->dashboardModel->getPedidosRecentes($this->resolveLojaId(), $limit);
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

    /**
     * Resolves the loja_id for the currently authenticated user.
     *
     * @return string
     * @throws \Exception
     */
    protected function resolveLojaId(): string
    {
        $user = auth()->user();
        $lojaId = $user->loja_id ?? null;

        if (!$lojaId) {
            throw new \Exception('Loja não encontrada para o usuário autenticado.', 403);
        }

        return $lojaId;
    }
}
