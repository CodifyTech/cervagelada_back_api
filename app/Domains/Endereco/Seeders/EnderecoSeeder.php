<?php

namespace App\Domains\Endereco\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\Endereco\Models\Endereco;
use App\Domains\Auth\Models\User;


class EnderecoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for Endereco.
     *
     * @return void
     */
    public function run(): void
    {
        // Garantir que existam Users para o relacionamento
        // $this->call(\App\Domains\Auth\Seeders\UserSeeder::class);

        // Para usar factories, crie o arquivo de factory correspondente:
        // Endereco::factory(10)->create();

        // Criar registros manualmente de exemplo:
        /*
        Endereco::create([
            'nome' => 'Exemplo de Endereco',
            // Adicione mais campos conforme necessário
        ]);
        */

        // Exemplo com relacionamentos:
        /*
        $relatedModel = RelatedModel::first();
        if ($relatedModel) {
            Endereco::create([
                'nome' => 'Exemplo com relação',
                'related_model_id' => $relatedModel->id,
                // Outros campos...
            ]);
        }
        */
    }
}
