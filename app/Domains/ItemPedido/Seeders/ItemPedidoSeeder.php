<?php

namespace App\Domains\ItemPedido\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\ItemPedido\Models\ItemPedido;
use App\Domains\Pedido\Models\Pedido;


class ItemPedidoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for ItemPedido.
     *
     * @return void
     */
    public function run(): void
    {
        $pedidos = Pedido::all();

        foreach ($pedidos as $pedido) {
            $lojaId = $pedido->loja_id;

            // Get products available in this store from pivot table
            $lojaProdutos = \DB::table('loja_produtos')
                ->where('loja_id', $lojaId)
                ->get();

            if ($lojaProdutos->isEmpty()) continue;

            $numItens = rand(1, 5);
            $subtotal = 0;
            $itensSelecionados = $lojaProdutos->random(min($numItens, $lojaProdutos->count()));

            foreach ($itensSelecionados as $lojaProduto) {
                $quantidade = rand(1, 4);
                $preco = $lojaProduto->preco_promocional ?? $lojaProduto->preco;
                $totalItem = $preco * $quantidade;
                $subtotal += $totalItem;

                ItemPedido::create([
                    'pedido_id' => $pedido->id,
                    'produto_id' => $lojaProduto->produto_id,
                    'quantidade_solicitada' => $quantidade,
                    'quantidade_final' => $quantidade,
                    'preco_unitario' => $preco,
                    'preco_total' => $totalItem,
                    'ajuste_preco' => 0,
                ]);
            }

            // Update pedido totals
            $total = $subtotal + $pedido->taxa_entrega;
            $pedido->update([
                'subtotal' => $subtotal,
                'total'    => $total,
            ]);

            // Sync pagamento valor if it exists
            \App\Domains\Pagamento\Models\Pagamento::where('pedido_id', $pedido->id)
                ->update(['valor' => $total]);
        }
    }
}
