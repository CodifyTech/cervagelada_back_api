<?php

namespace App\Domains\Promocao\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\Promocao\Models\Promocao;
use App\Domains\Loja\Models\Loja;


class PromocaoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for Promocao.
     *
     * @return void
     */
    public function run(): void
    {
        // Garantir que existam Lojas para o relacionamento
        // $this->call(\App\Domains\Loja\Seeders\LojaSeeder::class);

        // Para usar factories, crie o arquivo de factory correspondente:
        // Promocao::factory(10)->create();

        // Criar registros manualmente de exemplo:
        /*
        Promocao::create([
            'nome' => 'Exemplo de Promocao',
            // Adicione mais campos conforme necessário
        ]);
        */

        // Exemplo com relacionamentos:
        /*
        $relatedModel = RelatedModel::first();
        if ($relatedModel) {
            Promocao::create([
                'nome' => 'Exemplo com relação',
                'related_model_id' => $relatedModel->id,
                // Outros campos...
            ]);
        }
        */
    }
}
