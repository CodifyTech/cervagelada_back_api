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
        // Garantir que existam Lojas para o relacionamento
        // $this->call(\App\Domains\Loja\Seeders\LojaSeeder::class);

        // Para usar factories, crie o arquivo de factory correspondente:
        // Pedido::factory(10)->create();

        // Criar registros manualmente de exemplo:
        /*
        Pedido::create([
            'nome' => 'Exemplo de Pedido',
            // Adicione mais campos conforme necessário
        ]);
        */

        // Exemplo com relacionamentos:
        /*
        $relatedModel = RelatedModel::first();
        if ($relatedModel) {
            Pedido::create([
                'nome' => 'Exemplo com relação',
                'related_model_id' => $relatedModel->id,
                // Outros campos...
            ]);
        }
        */
    }
}
