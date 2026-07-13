<?php

namespace App\Domains\Relatorios\Models;

use App\Domains\Shared\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

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
            ->leftJoin('pagamentos', 'pagamentos.pedido_id', '=', 'pedidos.id')
            ->leftJoin('enderecos', 'enderecos.id', '=', 'pedidos.endereco_id')
            ->selectRaw('
                pedidos.id,
                UPPER(RIGHT(pedidos.id, 8)) as numero,
                users.name as cliente,
                lojas.nome_fantasia as loja,
                pedidos.status,
                pedidos.total,
                pagamentos.metodo as forma_pagamento,
                enderecos.cidade,
                pedidos.created_at as criado_em
            ');

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
     *
     * Nota: `produtos` não possui `loja_id` (relação N:N com `lojas` via
     * `loja_produtos`) e este relatório agrega vendas de um produto entre
     * todas as lojas. Como não há uma loja única por linha, usamos
     * `produtos.marca` como valor de exibição da coluna `loja`.
     */
    public function getProdutosMaisVendidos(array $filtros): array
    {
        $query = DB::table('item_pedidos')
            ->join('pedidos', 'item_pedidos.pedido_id', '=', 'pedidos.id')
            ->join('produtos', 'item_pedidos.produto_id', '=', 'produtos.id')
            ->selectRaw('
                produtos.id,
                produtos.nome as produto,
                produtos.marca as loja,
                COALESCE(SUM(item_pedidos.quantidade_final), 0) as quantidade_vendida,
                COALESCE(SUM(item_pedidos.preco_total), 0) as receita_total,
                CASE
                    WHEN SUM(item_pedidos.quantidade_final) > 0
                        THEN SUM(item_pedidos.preco_total) / SUM(item_pedidos.quantidade_final)
                    ELSE 0
                END as ticket_medio
            ')
            ->where('pedidos.status', '!=', 'cancelado')
            ->groupBy('produtos.id', 'produtos.nome', 'produtos.marca')
            ->orderByDesc('quantidade_vendida');

        if (! empty($filtros['de'])) {
            $query->where('pedidos.created_at', '>=', Carbon::parse($filtros['de'])->startOfDay());
        }
        if (! empty($filtros['ate'])) {
            $query->where('pedidos.created_at', '<=', Carbon::parse($filtros['ate'])->endOfDay());
        }

        $limite = intval($filtros['limite'] ?? 20);
        $data = $query->limit($limite)->get();

        return ['data' => $data->toArray(), 'total' => $data->count()];
    }

    /**
     * Relatório de sellers (lojas).
     *
     * O responsável por loja é resolvido via subquery (1 linha por loja),
     * evitando o produto cartesiano entre `users` e `pedidos` que antes
     * inflava `receita_total`/`total_pedidos` quando uma loja tinha mais de
     * um usuário vinculado.
     */
    public function getSellers(array $filtros): array
    {
        $responsaveisPorLoja = DB::table('users')
            ->select('loja_id', DB::raw('MIN(users.name) as nome_responsavel'))
            ->whereNotNull('loja_id')
            ->groupBy('loja_id');

        $query = DB::table('lojas')
            ->leftJoinSub($responsaveisPorLoja, 'responsaveis', 'responsaveis.loja_id', '=', 'lojas.id')
            ->leftJoin('pedidos', function ($join) use ($filtros) {
                $join->on('pedidos.loja_id', '=', 'lojas.id')
                    ->where('pedidos.status', '!=', 'cancelado');
                if (! empty($filtros['de'])) {
                    $join->where('pedidos.created_at', '>=', Carbon::parse($filtros['de'])->startOfDay());
                }
                if (! empty($filtros['ate'])) {
                    $join->where('pedidos.created_at', '<=', Carbon::parse($filtros['ate'])->endOfDay());
                }
            })
            ->selectRaw('
                lojas.id,
                lojas.nome_fantasia as loja,
                lojas.cnpj as cnpj,
                lojas.tipo_loja,
                lojas.cidade,
                lojas.estado,
                CASE WHEN lojas.ativo THEN "ativo" ELSE "inativo" END as status,
                lojas.created_at as criado_em,
                COUNT(DISTINCT pedidos.id) as total_pedidos,
                COALESCE(SUM(pedidos.total), 0) as receita,
                responsaveis.nome_responsavel as responsavel
            ')
            ->groupBy('lojas.id', 'lojas.nome_fantasia', 'lojas.cnpj', 'lojas.tipo_loja', 'lojas.cidade', 'lojas.estado', 'lojas.ativo', 'lojas.created_at', 'responsaveis.nome_responsavel');

        if (! empty($filtros['regiao'])) {
            $query->where('lojas.estado', $filtros['regiao']);
        }

        if (! empty($filtros['cidade'])) {
            $query->where('lojas.cidade', 'like', '%'.$filtros['cidade'].'%');
        }

        $query->orderByDesc('receita');

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
     * Relatório financeiro: uma linha por loja com receita bruta, taxa da
     * plataforma e receita líquida no período filtrado.
     */
    public function getFinanceiro(array $filtros): array
    {
        $query = DB::table('lojas')
            ->leftJoin('pedidos', function ($join) use ($filtros) {
                $join->on('pedidos.loja_id', '=', 'lojas.id')
                    ->where('pedidos.status', '!=', 'cancelado');
                if (! empty($filtros['de'])) {
                    $join->where('pedidos.created_at', '>=', Carbon::parse($filtros['de'])->startOfDay());
                }
                if (! empty($filtros['ate'])) {
                    $join->where('pedidos.created_at', '<=', Carbon::parse($filtros['ate'])->endOfDay());
                }
            })
            ->selectRaw('
                lojas.id,
                lojas.nome_fantasia as loja,
                lojas.taxa_comissao,
                COALESCE(SUM(pedidos.total), 0) as receita_bruta,
                COUNT(pedidos.id) as pedidos
            ')
            ->groupBy('lojas.id', 'lojas.nome_fantasia', 'lojas.taxa_comissao')
            ->orderByDesc('receita_bruta');

        if (! empty($filtros['loja_id'])) {
            $query->where('lojas.id', $filtros['loja_id']);
        }

        $perPage = intval($filtros['per_page'] ?? 30);
        $page = intval($filtros['page'] ?? 1);
        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        $taxaPlataformaFallback = (float) config('relatorios.taxa_plataforma');
        $periodo = $this->formatarPeriodoFinanceiro($filtros);

        $data = collect($paginated->items())
            ->map(fn ($row) => $this->montarLinhaFinanceiro($row, $taxaPlataformaFallback, $periodo))
            ->all();

        return [
            'data' => $data,
            'total' => $paginated->total(),
            'page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
        ];
    }

    /**
     * Monta uma linha do relatório financeiro garantindo que nenhum campo
     * monetário ou de contagem retorne null.
     *
     * A taxa da plataforma usa o percentual `taxa_comissao` da própria loja
     * (armazenado como percentual inteiro, ex.: 15.00 = 15%). Se a loja não
     * tiver uma taxa configurada (null ou <= 0), usa o fallback de
     * `config('relatorios.taxa_plataforma')` (já em fração).
     */
    private function montarLinhaFinanceiro(object $row, float $taxaPlataformaFallback, string $periodo): array
    {
        $receitaBruta = (float) ($row->receita_bruta ?? 0);
        $taxaComissao = (float) ($row->taxa_comissao ?? 0);
        $taxaFracao = $taxaComissao > 0 ? $taxaComissao / 100 : $taxaPlataformaFallback;
        $taxaValor = round($receitaBruta * $taxaFracao, 2);

        return [
            'loja' => $row->loja ?? '',
            'receita_bruta' => $receitaBruta,
            'taxa_plataforma' => $taxaValor,
            'receita_liquida' => round($receitaBruta - $taxaValor, 2),
            'pedidos' => (int) ($row->pedidos ?? 0),
            'periodo' => $periodo,
        ];
    }

    /**
     * Formata o intervalo de datas filtrado para exibição, nunca retornando
     * null/vazio.
     */
    private function formatarPeriodoFinanceiro(array $filtros): string
    {
        $de = ! empty($filtros['de']) ? Carbon::parse($filtros['de']) : null;
        $ate = ! empty($filtros['ate']) ? Carbon::parse($filtros['ate']) : null;

        if ($de && $ate) {
            return $de->format('d/m/Y').' - '.$ate->format('d/m/Y');
        }
        if ($de) {
            return 'A partir de '.$de->format('d/m/Y');
        }
        if ($ate) {
            return 'Até '.$ate->format('d/m/Y');
        }

        return 'Todos os períodos';
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
                ->leftJoin('pagamentos', 'pagamentos.pedido_id', '=', 'pedidos.id')
                ->leftJoin('enderecos', 'enderecos.id', '=', 'pedidos.endereco_id')
                ->selectRaw('
                    pedidos.id,
                    UPPER(RIGHT(pedidos.id, 8)) as numero,
                    users.name as cliente,
                    lojas.nome_fantasia as loja,
                    pedidos.status,
                    pedidos.total,
                    pagamentos.metodo as forma_pagamento,
                    enderecos.cidade,
                    pedidos.created_at as criado_em
                ');

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
                    produtos.nome as produto,
                    produtos.marca as cervejaria,
                    COALESCE(SUM(item_pedidos.quantidade_final), 0) as quantidade_vendida,
                    COALESCE(SUM(item_pedidos.preco_total), 0) as receita_total
                ')
                ->where('pedidos.status', '!=', 'cancelado')
                ->groupBy('produtos.id', 'produtos.nome', 'produtos.marca')
                ->orderByDesc('quantidade_vendida');

            if (! empty($filtros['de'])) {
                $query->where('pedidos.created_at', '>=', Carbon::parse($filtros['de'])->startOfDay());
            }
            if (! empty($filtros['ate'])) {
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
            $taxaPlataformaFallback = (float) config('relatorios.taxa_plataforma');
            $periodo = $this->formatarPeriodoFinanceiro($filtros);

            $query = DB::table('lojas')
                ->leftJoin('pedidos', function ($join) use ($filtros) {
                    $join->on('pedidos.loja_id', '=', 'lojas.id')
                        ->where('pedidos.status', '!=', 'cancelado');
                    if (! empty($filtros['de'])) {
                        $join->where('pedidos.created_at', '>=', Carbon::parse($filtros['de'])->startOfDay());
                    }
                    if (! empty($filtros['ate'])) {
                        $join->where('pedidos.created_at', '<=', Carbon::parse($filtros['ate'])->endOfDay());
                    }
                })
                ->selectRaw('
                    lojas.nome_fantasia as loja,
                    lojas.taxa_comissao,
                    COALESCE(SUM(pedidos.total), 0) as receita_bruta,
                    COUNT(pedidos.id) as pedidos
                ')
                ->groupBy('lojas.id', 'lojas.nome_fantasia', 'lojas.taxa_comissao')
                ->orderByDesc('receita_bruta');

            foreach ($query->lazy(200) as $row) {
                yield (object) $this->montarLinhaFinanceiro($row, $taxaPlataformaFallback, $periodo);
            }
        });
    }

    /**
     * Apply common order filters.
     */
    private function aplicarFiltrosPedidos($query, array $filtros): void
    {
        if (! empty($filtros['de'])) {
            $query->where('pedidos.created_at', '>=', Carbon::parse($filtros['de'])->startOfDay());
        }
        if (! empty($filtros['ate'])) {
            $query->where('pedidos.created_at', '<=', Carbon::parse($filtros['ate'])->endOfDay());
        }
        if (! empty($filtros['cidade'])) {
            $query->where('enderecos.cidade', 'like', '%'.$filtros['cidade'].'%');
        }
        if (! empty($filtros['status'])) {
            $query->where('pedidos.status', $filtros['status']);
        }
        if (! empty($filtros['loja_id'])) {
            $query->where('pedidos.loja_id', $filtros['loja_id']);
        }
    }
}
