<?php

namespace App\Domains\Loja\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\Loja\Models\HorarioLoja;


class HorarioLojaSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for HorarioLoja.
     *
     * @return void
     */
    public function run(): void
    {
        // Para usar factories, crie o arquivo de factory correspondente:
        // HorarioLoja::factory(10)->create();

        // Criar registros manualmente de exemplo:
        /*
        HorarioLoja::create([
            'nome' => 'Exemplo de HorarioLoja',
            // Adicione mais campos conforme necessário
        ]);
        */

        // Exemplo com relacionamentos:
        /*
        $relatedModel = RelatedModel::first();
        if ($relatedModel) {
            HorarioLoja::create([
                'nome' => 'Exemplo com relação',
                'related_model_id' => $relatedModel->id,
                // Outros campos...
            ]);
        }
        */
    }
}
