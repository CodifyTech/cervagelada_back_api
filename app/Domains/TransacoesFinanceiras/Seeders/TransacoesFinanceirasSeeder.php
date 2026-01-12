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
        // Garantir que existam Lojas para o relacionamento
        // $this->call(\App\Domains\Loja\Seeders\LojaSeeder::class);

        // Garantir que existam Pedidos para o relacionamento
        // $this->call(\App\Domains\Pedido\Seeders\PedidoSeeder::class);

        // Para usar factories, crie o arquivo de factory correspondente:
        // TransacoesFinanceiras::factory(10)->create();

        // Criar registros manualmente de exemplo:
        /*
        TransacoesFinanceiras::create([
            'nome' => 'Exemplo de TransacoesFinanceiras',
            // Adicione mais campos conforme necessário
        ]);
        */

        // Exemplo com relacionamentos:
        /*
        $relatedModel = RelatedModel::first();
        if ($relatedModel) {
            TransacoesFinanceiras::create([
                'nome' => 'Exemplo com relação',
                'related_model_id' => $relatedModel->id,
                // Outros campos...
            ]);
        }
        */
    }
}
