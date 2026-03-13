<?php

namespace App\Domains\Dashboard\Models;

use App\Domains\Shared\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardModel extends BaseModel
{
    /**
     * No table associated with this aggregator model.
     *
     * @var string|null
     */
    protected $table = null;

    /**
     * Get extended metrics: users by type, sellers activity, new registrations.
     */
    public function getMetricasUsuarios(): array
    {
        $hoje = Carbon::now();
        $inicioMes = $hoje->copy()->startOfMonth();
        $inicioMesPassado = $hoje->copy()->subMonth()->startOfMonth();
        $fimMesPassado = $hoje->copy()->subMonth()->endOfMonth();

        $totalPorRole = DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->selectRaw('roles.name as role, COUNT(DISTINCT users.id) as total')
            ->groupBy('roles.name')
            ->pluck('total', 'role')
            ->toArray();

        $novosEsteMes = DB::table('users')
            ->where('created_at', '>=', $inicioMes)
            ->count();

        $novosMesPassado = DB::table('users')
            ->whereBetween('created_at', [$inicioMesPassado, $fimMesPassado])
            ->count();

        return [
            'total_usuarios' => DB::table('users')->count(),
            'por_tipo' => $totalPorRole,
            'novos_este_mes' => $novosEsteMes,
            'variacao_cadastros' => $this->calculateVariacao($novosEsteMes, $novosMesPassado),
        ];
    }

    /**
     * Get sellers activity metrics.
     */
    public function getMetricasSellers(): array
    {
        $hoje = Carbon::now();
        $trintaDiasAtras = $hoje->copy()->subDays(30);

        $totalLojas = DB::table('lojas')->count();
        $lojasAtivas = DB::table('lojas')->where('ativo', 1)->count();
        $lojasInativas = $totalLojas - $lojasAtivas;

        $sellersAtivos = DB::table('lojas')
            ->where('ativo', 1)
            ->whereExists(function ($query) use ($trintaDiasAtras) {
                $query->from('pedidos')
                    ->whereColumn('pedidos.loja_id', 'lojas.id')
                    ->where('pedidos.created_at', '>=', $trintaDiasAtras);
            })
            ->count();

        $novasLojas = DB::table('lojas')
            ->where('created_at', '>=', $hoje->copy()->startOfMonth())
            ->count();

        return [
            'total_lojas' => $totalLojas,
            'lojas_ativas' => $lojasAtivas,
            'lojas_inativas' => $lojasInativas,
            'sellers_ativos_30d' => $sellersAtivos,
            'novas_lojas_mes' => $novasLojas,
        ];
    }

    /**
     * Get main metrics (receita_total, total_pedidos, ticket_medio).
     * Compares current month with previous month.
     */
    public function getMetricas(): array
    {
        $hoje = Carbon::now();
        $mesAtual = $hoje->month;
        $anoAtual = $hoje->year;

        $mesPassado = $hoje->copy()->subMonth()->month;
        $anoPassado = $hoje->copy()->subMonth()->year;

        $statsAtual = $this->getStatsByPeriod($mesAtual, $anoAtual);
        $statsPassado = $this->getStatsByPeriod($mesPassado, $anoPassado);

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
            ],
        ];
    }

    /**
     * Get monthly sales (revenue) grouped by month for the given year.
     */
    public function getVendasMensais(int $ano): array
    {
        $vendas = DB::table('pedidos')
            ->selectRaw('MONTH(created_at) as mes, SUM(total) as valor')
            ->where('loja_id', config('cdf.active_loja_id'))
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
    public function getPedidosPorMes(int $ano): array
    {
        $pedidos = DB::table('pedidos')
            ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
            ->where('loja_id', config('cdf.active_loja_id'))
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
    public function getCategoriasMaisVendidas(): array
    {
        // Using 'marca' as a proxy for category as per implementation plan
        return DB::table('item_pedidos')
            ->join('pedidos', 'item_pedidos.pedido_id', '=', 'pedidos.id')
            ->join('produtos', 'item_pedidos.produto_id', '=', 'produtos.id')
            ->selectRaw('produtos.marca as categoria, SUM(item_pedidos.quantidade_final) as vendas')
            ->where('pedidos.loja_id', config('cdf.active_loja_id'))
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
    public function getTopProdutos(int $limit = 5): array
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
            ->where('pedidos.loja_id', config('cdf.active_loja_id'))
            ->where('pedidos.status', '!=', 'cancelado')
            ->whereMonth('pedidos.created_at', $mesAtual)
            ->whereYear('pedidos.created_at', $anoAtual)
            ->groupBy('produtos.id', 'produtos.nome', 'produtos.marca')
            ->orderByDesc('vendas')
            ->limit($limit)
            ->get();

        return $top->map(function ($item) use ($mesPassado, $anoPassado) {
            // Calculate growth vs last month for this specific product
            $vendasPassado = DB::table('item_pedidos')
                ->join('pedidos', 'item_pedidos.pedido_id', '=', 'pedidos.id')
                ->where('pedidos.loja_id', config('cdf.active_loja_id'))
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
    public function getPedidosRecentes(int $limit = 5): array
    {
        return DB::table('pedidos')
            ->join('users', 'pedidos.user_id', '=', 'users.id')
            ->select('pedidos.id', 'users.name as cliente', 'pedidos.total as valor', 'pedidos.status', 'pedidos.created_at as data')
            ->where('pedidos.loja_id', config('cdf.active_loja_id'))
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
    private function getStatsByPeriod(int $mes, int $ano): array
    {
        $data = DB::table('pedidos')
            ->selectRaw('SUM(total) as vendas, COUNT(*) as pedidos')
            ->where('loja_id', config('cdf.active_loja_id'))
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
     * Get admin-level metrics: total users by type, sellers, orders by status, revenue, new signups.
     */
    public function getMetricasAdmin(): array
    {
        $hoje = Carbon::now();
        $inicioMes = $hoje->copy()->startOfMonth();
        $inicio7d = $hoje->copy()->subDays(7);
        $inicio30d = $hoje->copy()->subDays(30);

        $pedidosPorStatus = DB::table('pedidos')
            ->selectRaw('status, COUNT(*) as total')
            ->where('created_at', '>=', $inicio30d)
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $receitaTotal = DB::table('pedidos')
            ->where('status', '!=', 'cancelado')
            ->whereMonth('created_at', $hoje->month)
            ->whereYear('created_at', $hoje->year)
            ->sum('total');

        $receitaMesPassado = DB::table('pedidos')
            ->where('status', '!=', 'cancelado')
            ->whereMonth('created_at', $hoje->copy()->subMonth()->month)
            ->whereYear('created_at', $hoje->copy()->subMonth()->year)
            ->sum('total');

        $totalPedidosMes = DB::table('pedidos')
            ->whereMonth('created_at', $hoje->month)
            ->whereYear('created_at', $hoje->year)
            ->count();

        $ticketMedio = $totalPedidosMes > 0 ? floatval($receitaTotal) / $totalPedidosMes : 0;

        return [
            'usuarios' => $this->getMetricasUsuarios(),
            'sellers' => $this->getMetricasSellers(),
            'pedidos_por_status_30d' => $pedidosPorStatus,
            'receita_total_mes' => floatval($receitaTotal),
            'variacao_receita' => $this->calculateVariacao($receitaTotal, $receitaMesPassado),
            'ticket_medio' => round($ticketMedio, 2),
            'total_pedidos_mes' => $totalPedidosMes,
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
