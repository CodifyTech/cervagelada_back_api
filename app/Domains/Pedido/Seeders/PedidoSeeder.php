<?php

namespace App\Domains\Pedido\Seeders;

use App\Domains\Auth\Models\User;
use App\Domains\Endereco\Models\Endereco;
use App\Domains\Loja\Models\Loja;
use App\Domains\Pagamento\Models\Pagamento;
use App\Domains\Pedido\Models\Pedido;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PedidoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for Pedido.
     */
    public function run(): void
    {
        $lojas = Loja::all();
        $users = User::all();

        // Statuses weighted toward completed orders for realistic report data
        $statuses = [
            'aguardando_pagamento',
            'recebido',
            'aceito',
            'preparando',
            'pronto',
            'em_rota',
            'entregue', 'entregue', 'entregue', 'entregue', 'entregue',
            'cancelado',
        ];

        $metodos = ['pix', 'pix', 'pix', 'cartao', 'cartao', 'boleto'];

        $hoje = Carbon::now();

        foreach ($lojas as $loja) {
            $numPedidos = rand(20, 40);

            for ($i = 0; $i < $numPedidos; $i++) {
                $user = $users->random();
                $dataCriacao = $hoje->copy()->subMonths(rand(0, 11))->subDays(rand(0, 28));
                $status = $statuses[array_rand($statuses)];

                $endereco = Endereco::where('user_id', $user->id)->first();

                $pedido = Pedido::create([
                    'loja_id' => $loja->id,
                    'user_id' => $user->id,
                    'endereco_id' => $endereco?->id,
                    'subtotal' => 0,
                    'taxa_entrega' => rand(5, 20),
                    'total' => 0,
                    'status' => $status,
                    'created_at' => $dataCriacao,
                    'updated_at' => $dataCriacao,
                ]);

                // Create pagamento for all orders that moved past aguardando_pagamento
                if ($status !== 'aguardando_pagamento') {
                    $isPago = $status !== 'cancelado';
                    Pagamento::create([
                        'pedido_id' => $pedido->id,
                        'loja_id' => $loja->id,
                        'metodo' => $metodos[array_rand($metodos)],
                        'status' => $isPago ? 'pago' : 'cancelado',
                        'valor' => 0, // Updated by ItemPedidoSeeder
                        'pago_em' => $isPago ? $dataCriacao->copy()->addMinutes(rand(2, 15)) : null,
                        'created_at' => $dataCriacao,
                        'updated_at' => $dataCriacao,
                    ]);
                }
            }
        }
    }
}
