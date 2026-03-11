<?php

namespace App\Domains\Relatorios\Models;

use App\Domains\Shared\Models\BaseModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Carbon\Carbon;

class RelatoriosModel extends BaseModel
{
    protected $table = null;

    /**
     * Relatório de pedidos com filtros opcionais.
     */
    public function getPedidos(array $filtros): array
    {
        $query = DB::table('pedidos')
            ->leftJoin('users', 'pedidos.user_id', '=', 'users.id')
            ->leftJoin('lojas', 'pedidos.loja_id', '=', 'lojas.id')
            ->select(
                'pedidos.id',
                'users.name as cliente',
                'lojas.nome_fantasia as loja',
                'pedidos.status',
                'pedidos.total',
                'pedidos.forma_pagamento',
                'pedidos.cidade',
                'pedidos.created_at'
            );

        $this->aplicarFiltrosPedidos($query, $filtros);

        $query->orderByDesc('pedidos.created_at');

        $perPage = intval($filtros['per_page'] ?? 15);
        $page = intval($filtros['page'] ?? 1);
        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginated->items(),
            'total' => $paginated->total(),
            'page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
        ];
    }

    /**
     * Produtos mais vendidos com filtros opcionais.
     */
    public function getProdutosMaisVendidos(array $filtros): array
    {
        $query = DB::table('item_pedidos')
            ->join('pedidos', 'item_pedidos.pedido_id', '=', 'pedidos.id')
            ->join('produtos', 'item_pedidos.produto_id', '=', 'produtos.id')
            ->selectRaw('
                produtos.id,
                produtos.nome,
                produtos.marca as cervejaria,
                SUM(item_pedidos.quantidade_final) as total_vendido,
                SUM(item_pedidos.preco_total) as receita_total,
                COUNT(DISTINCT pedidos.id) as total_pedidos
            ')
            ->where('pedidos.status', '!=', 'cancelado')
            ->groupBy('produtos.id', 'produtos.nome', 'produtos.marca')
            ->orderByDesc('total_vendido');

        if (!empty($filtros['de'])) {
            $query->where('pedidos.created_at', '>=', Carbon::parse($filtros['de'])->startOfDay());
        }
        if (!empty($filtros['ate'])) {
            $query->where('pedidos.created_at', '<=', Carbon::parse($filtros['ate'])->endOfDay());
        }

        $limite = intval($filtros['limite'] ?? 20);
        $data = $query->limit($limite)->get();

        return ['data' => $data->toArray(), 'total' => $data->count()];
    }

    /**
     * Relatório de sellers (lojas).
     */
    public function getSellers(array $filtros): array
    {
        $query = DB::table('lojas')
            ->leftJoin('users', 'lojas.user_id', '=', 'users.id')
            ->selectRaw('
                lojas.id,
                lojas.nome_fantasia,
                lojas.tipo_loja,
                lojas.cidade,
                lojas.estado,
                lojas.ativo,
                lojas.created_at,
                COUNT(DISTINCT pedidos.id) as total_pedidos,
                COALESCE(SUM(pedidos.total), 0) as receita_total,
                users.name as responsavel
            ')
            ->leftJoin('pedidos', function ($join) use ($filtros) {
                $join->on('pedidos.loja_id', '=', 'lojas.id')
                    ->where('pedidos.status', '!=', 'cancelado');
                if (!empty($filtros['de'])) {
                    $join->where('pedidos.created_at', '>=', Carbon::parse($filtros['de'])->startOfDay());
                }
                if (!empty($filtros['ate'])) {
                    $join->where('pedidos.created_at', '<=', Carbon::parse($filtros['ate'])->endOfDay());
                }
            })
            ->groupBy('lojas.id', 'lojas.nome_fantasia', 'lojas.tipo_loja', 'lojas.cidade', 'lojas.estado', 'lojas.ativo', 'lojas.created_at', 'users.name');

        if (!empty($filtros['regiao'])) {
            $query->where('lojas.estado', $filtros['regiao']);
        }

        $query->orderByDesc('receita_total');

        $perPage = intval($filtros['per_page'] ?? 15);
        $page = intval($filtros['page'] ?? 1);
        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginated->items(),
            'total' => $paginated->total(),
            'page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
        ];
    }

    /**
     * Relatório financeiro.
     */
    public function getFinanceiro(array $filtros): array
    {
        $query = DB::table('pedidos')
            ->selectRaw('
                DATE(created_at) as data,
                COUNT(*) as total_pedidos,
                SUM(CASE WHEN status != "cancelado" THEN total ELSE 0 END) as receita,
                SUM(CASE WHEN status = "cancelado" THEN 1 ELSE 0 END) as cancelamentos,
                AVG(CASE WHEN status != "cancelado" THEN total ELSE NULL END) as ticket_medio
            ')
            ->groupByRaw('DATE(created_at)')
            ->orderByDesc('data');

        if (!empty($filtros['de'])) {
            $query->where('created_at', '>=', Carbon::parse($filtros['de'])->startOfDay());
        }
        if (!empty($filtros['ate'])) {
            $query->where('created_at', '<=', Carbon::parse($filtros['ate'])->endOfDay());
        }

        $perPage = intval($filtros['per_page'] ?? 30);
        $page = intval($filtros['page'] ?? 1);
        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        $totais = DB::table('pedidos')
            ->selectRaw('
                SUM(CASE WHEN status != "cancelado" THEN total ELSE 0 END) as receita_total,
                COUNT(*) as total_pedidos,
                AVG(CASE WHEN status != "cancelado" THEN total ELSE NULL END) as ticket_medio_geral
            ')
            ->when(!empty($filtros['de']), fn($q) => $q->where('created_at', '>=', Carbon::parse($filtros['de'])->startOfDay()))
            ->when(!empty($filtros['ate']), fn($q) => $q->where('created_at', '<=', Carbon::parse($filtros['ate'])->endOfDay()))
            ->first();

        return [
            'data' => $paginated->items(),
            'total' => $paginated->total(),
            'page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'totais' => $totais,
        ];
    }

    /**
     * Stream de pedidos para exportação CSV usando LazyCollection.
     */
    public function streamPedidosCsv(array $filtros): LazyCollection
    {
        return LazyCollection::make(function () use ($filtros) {
            $query = DB::table('pedidos')
                ->leftJoin('users', 'pedidos.user_id', '=', 'users.id')
                ->leftJoin('lojas', 'pedidos.loja_id', '=', 'lojas.id')
                ->select(
                    'pedidos.id',
                    'users.name as cliente',
                    'lojas.nome_fantasia as loja',
                    'pedidos.status',
                    'pedidos.total',
                    'pedidos.forma_pagamento',
                    'pedidos.cidade',
                    'pedidos.created_at'
                );

            $this->aplicarFiltrosPedidos($query, $filtros);
            $query->orderByDesc('pedidos.created_at');

            foreach ($query->lazy(200) as $row) {
                yield $row;
            }
        });
    }

    /**
     * Stream de produtos mais vendidos para exportação CSV.
     */
    public function streamProdutosCsv(array $filtros): LazyCollection
    {
        return LazyCollection::make(function () use ($filtros) {
            $query = DB::table('item_pedidos')
                ->join('pedidos', 'item_pedidos.pedido_id', '=', 'pedidos.id')
                ->join('produtos', 'item_pedidos.produto_id', '=', 'produtos.id')
                ->selectRaw('
                    produtos.nome,
                    produtos.marca as cervejaria,
                    SUM(item_pedidos.quantidade_final) as total_vendido,
                    SUM(item_pedidos.preco_total) as receita_total
                ')
                ->where('pedidos.status', '!=', 'cancelado')
                ->groupBy('produtos.id', 'produtos.nome', 'produtos.marca')
                ->orderByDesc('total_vendido');

            if (!empty($filtros['de'])) {
                $query->where('pedidos.created_at', '>=', Carbon::parse($filtros['de'])->startOfDay());
            }
            if (!empty($filtros['ate'])) {
                $query->where('pedidos.created_at', '<=', Carbon::parse($filtros['ate'])->endOfDay());
            }

            foreach ($query->lazy(200) as $row) {
                yield $row;
            }
        });
    }

    /**
     * Stream financeiro para exportação CSV.
     */
    public function streamFinanceiroCsv(array $filtros): LazyCollection
    {
        return LazyCollection::make(function () use ($filtros) {
            $query = DB::table('pedidos')
                ->selectRaw('
                    DATE(created_at) as data,
                    COUNT(*) as total_pedidos,
                    SUM(CASE WHEN status != "cancelado" THEN total ELSE 0 END) as receita,
                    SUM(CASE WHEN status = "cancelado" THEN 1 ELSE 0 END) as cancelamentos,
                    AVG(CASE WHEN status != "cancelado" THEN total ELSE NULL END) as ticket_medio
                ')
                ->groupByRaw('DATE(created_at)')
                ->orderByDesc('data');

            if (!empty($filtros['de'])) {
                $query->where('created_at', '>=', Carbon::parse($filtros['de'])->startOfDay());
            }
            if (!empty($filtros['ate'])) {
                $query->where('created_at', '<=', Carbon::parse($filtros['ate'])->endOfDay());
            }

            foreach ($query->lazy(200) as $row) {
                yield $row;
            }
        });
    }

    /**
     * Apply common order filters.
     */
    private function aplicarFiltrosPedidos($query, array $filtros): void
    {
        if (!empty($filtros['de'])) {
            $query->where('pedidos.created_at', '>=', Carbon::parse($filtros['de'])->startOfDay());
        }
        if (!empty($filtros['ate'])) {
            $query->where('pedidos.created_at', '<=', Carbon::parse($filtros['ate'])->endOfDay());
        }
        if (!empty($filtros['cidade'])) {
            $query->where('pedidos.cidade', 'like', '%' . $filtros['cidade'] . '%');
        }
        if (!empty($filtros['status'])) {
            $query->where('pedidos.status', $filtros['status']);
        }
        if (!empty($filtros['loja_id'])) {
            $query->where('pedidos.loja_id', $filtros['loja_id']);
        }
    }
}
