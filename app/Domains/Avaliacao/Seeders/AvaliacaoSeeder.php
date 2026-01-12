<?php

namespace App\Domains\Avaliacao\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\Avaliacao\Models\Avaliacao;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Auth\Models\User;
use App\Domains\Loja\Models\Loja;


class AvaliacaoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for Avaliacao.
     *
     * @return void
     */
    public function run(): void
    {
        // Garantir que existam Pedidos para o relacionamento
        // $this->call(\App\Domains\Pedido\Seeders\PedidoSeeder::class);

        // Garantir que existam Users para o relacionamento
        // $this->call(\App\Domains\Auth\Seeders\UserSeeder::class);

        // Garantir que existam Lojas para o relacionamento
        // $this->call(\App\Domains\Loja\Seeders\LojaSeeder::class);

        // Para usar factories, crie o arquivo de factory correspondente:
        // Avaliacao::factory(10)->create();

        // Criar registros manualmente de exemplo:
        /*
        Avaliacao::create([
            'nome' => 'Exemplo de Avaliacao',
            // Adicione mais campos conforme necessário
        ]);
        */

        // Exemplo com relacionamentos:
        /*
        $relatedModel = RelatedModel::first();
        if ($relatedModel) {
            Avaliacao::create([
                'nome' => 'Exemplo com relação',
                'related_model_id' => $relatedModel->id,
                // Outros campos...
            ]);
        }
        */
    }
}
