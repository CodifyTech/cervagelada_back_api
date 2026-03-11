<?php

namespace App\Domains\Pedido\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Loja\Models\Loja;


class PedidoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for Pedido.
     *
     * @return void
     */
    public function run(): void
    {
        $lojas = Loja::all();
        $users = \App\Domains\Auth\Models\User::all();
        
        // We will create orders for the last 12 months to populate the charts
        $hoje = \Carbon\Carbon::now();

        foreach ($lojas as $loja) {
            // Create between 20 and 40 orders per loja distributed over 12 months
            $numPedidos = rand(20, 40);
            
            for ($i = 0; $i < $numPedidos; $i++) {
                $user = $users->random();
                $dataCriacao = $hoje->copy()->subMonths(rand(0, 11))->subDays(rand(0, 28));
                
                Pedido::create([
                    'loja_id' => $loja->id,
                    'user_id' => $user->id,
                    'subtotal' => 0, // Will be updated by ItemPedidoSeeder or after item creation
                    'taxa_entrega' => rand(5, 15),
                    'total' => 0, // Will be updated
                    'status' => ['pendente', 'preparando', 'pronto', 'em_rota', 'entregue', 'entregue', 'entregue'][rand(0, 6)],
                    'created_at' => $dataCriacao,
                    'updated_at' => $dataCriacao,
                ]);
            }
        }
    }
}
