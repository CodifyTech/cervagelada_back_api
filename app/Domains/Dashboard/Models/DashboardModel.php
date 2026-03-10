<?php

namespace App\Domains\Dashboard\Models;

use App\Domains\Shared\Models\BaseModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardModel extends BaseModel
{
    /**
     * No table associated with this aggregator model.
     *
     * @var string|null
     */
    protected $table = null;

    /**
     * Get main metrics (receita_total, total_pedidos, ticket_medio).
     * Compares current month with previous month.
     */
    public function getMetricas(string $lojaId): array
    {
        $hoje = Carbon::now();
        $mesAtual = $hoje->month;
        $anoAtual = $hoje->year;

        $mesPassado = $hoje->copy()->subMonth()->month;
        $anoPassado = $hoje->copy()->subMonth()->year;

        $statsAtual = $this->getStatsByPeriod($lojaId, $mesAtual, $anoAtual);
        $statsPassado = $this->getStatsByPeriod($lojaId, $mesPassado, $anoPassado);

        return [
            'receita_total' => [
                'valor' => $statsAtual['vendas'],
                'variacao' => $this->calculateVariacao($statsAtual['vendas'], $statsPassado['vendas']),
                'descricao' => 'vs. mês anterior',
            ],
            'total_pedidos' => [
                'valor' => $statsAtual['pedidos'],
                'variacao' => $this->calculateVariacao($statsAtual['pedidos'], $statsPassado['pedidos']),
                'descricao' => 'vs. mês anterior',
            ],
            'ticket_medio' => [
                'valor' => $statsAtual['ticket_medio'],
                'variacao' => $this->calculateVariacao($statsAtual['ticket_medio'], $statsPassado['ticket_medio']),
                'descricao' => 'vs. mês anterior',
            ]
        ];
    }

    /**
     * Get monthly sales (revenue) grouped by month for the given year.
     */
    public function getVendasMensais(string $lojaId, int $ano): array
    {
        $vendas = DB::table('pedidos')
            ->selectRaw('MONTH(created_at) as mes, SUM(total) as valor')
            ->where('loja_id', $lojaId)
            ->whereYear('created_at', $ano)
            ->where('status', '!=', 'cancelado')
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('valor', 'mes')
            ->toArray();

        $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $labels = [];
        $valores = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = $meses[$i - 1];
            $valores[] = floatval($vendas[$i] ?? 0);
        }

        return [
            'labels' => $labels,
            'valores' => $valores,
        ];
    }

    /**
     * Get order count per month for the given year.
     */
    public function getPedidosPorMes(string $lojaId, int $ano): array
    {
        $pedidos = DB::table('pedidos')
            ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
            ->where('loja_id', $lojaId)
            ->whereYear('created_at', $ano)
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        $valores = [];
        for ($i = 1; $i <= 12; $i++) {
            $valores[] = intval($pedidos[$i] ?? 0);
        }

        return $valores;
    }

    /**
     * Top selling categories based on product brand (marca).
     */
    public function getCategoriasMaisVendidas(string $lojaId): array
    {
        // Using 'marca' as a proxy for category as per implementation plan
        return DB::table('item_pedidos')
            ->join('pedidos', 'item_pedidos.pedido_id', '=', 'pedidos.id')
            ->join('produtos', 'item_pedidos.produto_id', '=', 'produtos.id')
            ->selectRaw('produtos.marca as categoria, SUM(item_pedidos.quantidade_final) as vendas')
            ->where('pedidos.loja_id', $lojaId)
            ->where('pedidos.status', '!=', 'cancelado')
            ->groupBy('produtos.marca')
            ->orderByDesc('vendas')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'categoria' => $item->categoria ?? 'Outros',
                    'vendas' => intval($item->vendas),
                ];
            })
            ->toArray();
    }

    /**
     * Top 5 selling products with details.
     */
    public function getTopProdutos(string $lojaId, int $limit = 5): array
    {
        $hoje = Carbon::now();
        $mesAtual = $hoje->month;
        $anoAtual = $hoje->year;

        $mesPassado = $hoje->copy()->subMonth()->month;
        $anoPassado = $hoje->copy()->subMonth()->year;

        $top = DB::table('item_pedidos')
            ->join('pedidos', 'item_pedidos.pedido_id', '=', 'pedidos.id')
            ->join('produtos', 'item_pedidos.produto_id', '=', 'produtos.id')
            ->selectRaw('
                produtos.id,
                produtos.nome,
                produtos.marca as cervejaria,
                SUM(item_pedidos.quantidade_final) as vendas,
                SUM(item_pedidos.preco_total) as receita
            ')
            ->where('pedidos.loja_id', $lojaId)
            ->where('pedidos.status', '!=', 'cancelado')
            ->whereMonth('pedidos.created_at', $mesAtual)
            ->whereYear('pedidos.created_at', $anoAtual)
            ->groupBy('produtos.id', 'produtos.nome', 'produtos.marca')
            ->orderByDesc('vendas')
            ->limit($limit)
            ->get();

        return $top->map(function ($item) use ($lojaId, $mesPassado, $anoPassado) {
            // Calculate growth vs last month for this specific product
            $vendasPassado = DB::table('item_pedidos')
                ->join('pedidos', 'item_pedidos.pedido_id', '=', 'pedidos.id')
                ->where('pedidos.loja_id', $lojaId)
                ->where('item_pedidos.produto_id', $item->id)
                ->where('pedidos.status', '!=', 'cancelado')
                ->whereMonth('pedidos.created_at', $mesPassado)
                ->whereYear('pedidos.created_at', $anoPassado)
                ->sum('item_pedidos.quantidade_final');

            return [
                'id' => $item->id,
                'nome' => $item->nome,
                'cervejaria' => $item->cervejaria,
                'vendas' => intval($item->vendas),
                'receita' => floatval($item->receita),
                'crescimento' => $this->calculateVariacao($item->vendas, $vendasPassado),
            ];
        })->toArray();
    }

    /**
     * Most recent orders.
     */
    public function getPedidosRecentes(string $lojaId, int $limit = 5): array
    {
        return DB::table('pedidos')
            ->join('users', 'pedidos.user_id', '=', 'users.id')
            ->select('pedidos.id', 'users.name as cliente', 'pedidos.total as valor', 'pedidos.status', 'pedidos.created_at as data')
            ->where('pedidos.loja_id', $lojaId)
            ->orderByDesc('pedidos.created_at')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => substr($item->id, -8), // Shortened ID
                    'id_completo' => $item->id,
                    'cliente' => $item->cliente,
                    'valor' => floatval($item->valor),
                    'status' => ucfirst($item->status),
                    'data' => Carbon::parse($item->data)->format('d/m/Y'),
                ];
            })
            ->toArray();
    }

    /**
     * Helper to get stats for a specific period.
     */
    private function getStatsByPeriod(string $lojaId, int $mes, int $ano): array
    {
        $data = DB::table('pedidos')
            ->selectRaw('SUM(total) as vendas, COUNT(*) as pedidos')
            ->where('loja_id', $lojaId)
            ->where('status', '!=', 'cancelado')
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $ano)
            ->first();

        $vendas = floatval($data->vendas ?? 0);
        $pedidos = intval($data->pedidos ?? 0);

        return [
            'vendas' => $vendas,
            'pedidos' => $pedidos,
            'ticket_medio' => $pedidos > 0 ? $vendas / $pedidos : 0,
        ];
    }

    /**
     * Helper to calculate percentage variation.
     */
    private function calculateVariacao($atual, $passado): float
    {
        if ($passado == 0) {
            return $atual > 0 ? 100 : 0;
        }

        return round((($atual - $passado) / $passado) * 100, 1);
    }
}
