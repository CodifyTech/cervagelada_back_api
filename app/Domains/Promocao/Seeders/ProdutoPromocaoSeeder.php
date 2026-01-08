<?php

namespace App\Domains\Promocao\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\Promocao\Models\ProdutoPromocao;


class ProdutoPromocaoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for ProdutoPromocao.
     *
     * @return void
     */
    public function run(): void
    {
        // Para usar factories, crie o arquivo de factory correspondente:
        // ProdutoPromocao::factory(10)->create();

        // Criar registros manualmente de exemplo:
        /*
        ProdutoPromocao::create([
            'nome' => 'Exemplo de ProdutoPromocao',
            // Adicione mais campos conforme necessário
        ]);
        */

        // Exemplo com relacionamentos:
        /*
        $relatedModel = RelatedModel::first();
        if ($relatedModel) {
            ProdutoPromocao::create([
                'nome' => 'Exemplo com relação',
                'related_model_id' => $relatedModel->id,
                // Outros campos...
            ]);
        }
        */
    }
}
