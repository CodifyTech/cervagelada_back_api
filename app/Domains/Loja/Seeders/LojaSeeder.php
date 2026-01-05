<?php

namespace App\Domains\Loja\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\Loja\Models\Loja;


class LojaSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for Loja.
     *
     * @return void
     */
    public function run(): void
    {
        // Para usar factories, crie o arquivo de factory correspondente:
        // Loja::factory(10)->create();

        // Criar registros manualmente de exemplo:
        /*
        Loja::create([
            'nome' => 'Exemplo de Loja',
            // Adicione mais campos conforme necessário
        ]);
        */

        // Exemplo com relacionamentos:
        /*
        $relatedModel = RelatedModel::first();
        if ($relatedModel) {
            Loja::create([
                'nome' => 'Exemplo com relação',
                'related_model_id' => $relatedModel->id,
                // Outros campos...
            ]);
        }
        */
    }
}
