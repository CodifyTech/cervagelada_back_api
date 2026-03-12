<?php

namespace App\Domains\TransacoesFinanceiras\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\TransacoesFinanceiras\Models\TransacoesFinanceiras;
use App\Domains\Loja\Models\Loja;
use App\Domains\Pedido\Models\Pedido;


class TransacoesFinanceirasSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for TransacoesFinanceiras.
     *
     * @return void
     */
    public function run(): void
    {
        $pedidosEntregues = Pedido::where('status', 'entregue')->with('loja')->get();

        foreach ($pedidosEntregues as $pedido) {
            if (!$pedido->loja || $pedido->total <= 0) continue;

            $comissao = round($pedido->total * ($pedido->loja->taxa_comissao / 100), 2);
            $repasse  = round($pedido->total - $comissao - $pedido->taxa_entrega, 2);

            // Comissão da plataforma
            TransacoesFinanceiras::create([
                'loja_id'      => $pedido->loja_id,
                'pedido_id'    => $pedido->id,
                'tipo'         => 'comissao',
                'valor'        => $comissao,
                'descricao'    => 'Comissão sobre pedido #' . substr($pedido->id, 0, 8),
                'liquidado'    => true,
                'liquidado_em' => $pedido->updated_at,
                'created_at'   => $pedido->created_at,
                'updated_at'   => $pedido->updated_at,
            ]);

            // Repasse para a loja
            if ($repasse > 0) {
                TransacoesFinanceiras::create([
                    'loja_id'      => $pedido->loja_id,
                    'pedido_id'    => $pedido->id,
                    'tipo'         => 'credito',
                    'valor'        => $repasse,
                    'descricao'    => 'Repasse do pedido #' . substr($pedido->id, 0, 8),
                    'liquidado'    => true,
                    'liquidado_em' => $pedido->updated_at,
                    'created_at'   => $pedido->created_at,
                    'updated_at'   => $pedido->updated_at,
                ]);
            }
        }
    }
}
