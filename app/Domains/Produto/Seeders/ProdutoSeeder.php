<?php

namespace App\Domains\Produto\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\Produto\Models\Produto;


class ProdutoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for Produto.
     *
     * @return void
     */
    public function run(): void
    {
        // Para usar factories, crie o arquivo de factory correspondente:
        // Produto::factory(10)->create();

        // Criar registros manualmente de exemplo:
        /*
        Produto::create([
            'nome' => 'Exemplo de Produto',
            // Adicione mais campos conforme necessário
        ]);
        */

        // Exemplo com relacionamentos:
        /*
        $relatedModel = RelatedModel::first();
        if ($relatedModel) {
            Produto::create([
                'nome' => 'Exemplo com relação',
                'related_model_id' => $relatedModel->id,
                // Outros campos...
            ]);
        }
        */
    }
}
