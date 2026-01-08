<?php

namespace App\Domains\Noticias\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\Noticias\Models\Noticias;


class NoticiasSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for Noticias.
     *
     * @return void
     */
    public function run(): void
    {
        // Para usar factories, crie o arquivo de factory correspondente:
        // Noticias::factory(10)->create();

        // Criar registros manualmente de exemplo:
        /*
        Noticias::create([
            'nome' => 'Exemplo de Noticias',
            // Adicione mais campos conforme necessário
        ]);
        */

        // Exemplo com relacionamentos:
        /*
        $relatedModel = RelatedModel::first();
        if ($relatedModel) {
            Noticias::create([
                'nome' => 'Exemplo com relação',
                'related_model_id' => $relatedModel->id,
                // Outros campos...
            ]);
        }
        */
    }
}
