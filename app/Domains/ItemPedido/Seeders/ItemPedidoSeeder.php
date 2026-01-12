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
        // Garantir que existam Pedidos para o relacionamento
        // $this->call(\App\Domains\Pedido\Seeders\PedidoSeeder::class);

        // Para usar factories, crie o arquivo de factory correspondente:
        // ItemPedido::factory(10)->create();

        // Criar registros manualmente de exemplo:
        /*
        ItemPedido::create([
            'nome' => 'Exemplo de ItemPedido',
            // Adicione mais campos conforme necessário
        ]);
        */

        // Exemplo com relacionamentos:
        /*
        $relatedModel = RelatedModel::first();
        if ($relatedModel) {
            ItemPedido::create([
                'nome' => 'Exemplo com relação',
                'related_model_id' => $relatedModel->id,
                // Outros campos...
            ]);
        }
        */
    }
}
