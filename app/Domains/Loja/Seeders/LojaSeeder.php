<?php

namespace App\Domains\Loja\Seeders;

use App\Domains\Auth\Models\User;
use App\Domains\Loja\Models\Loja;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LojaSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for Loja.
     */
    public function run(): void
    {
        $lojas = [
            ['nome_fantasia' => 'Cerva Express', 'tipo_loja' => 'distribuidor'],
            ['nome_fantasia' => 'Mestre Malte', 'tipo_loja' => 'cervejaria'],
            ['nome_fantasia' => 'Empório Lúpulo', 'tipo_loja' => 'distribuidor'],
            ['nome_fantasia' => 'Draft House', 'tipo_loja' => 'cervejaria'],
            ['nome_fantasia' => 'Santo Gole', 'tipo_loja' => 'distribuidor'],
            ['nome_fantasia' => 'Cervejaria Artesanal 01', 'tipo_loja' => 'cervejaria'],
            ['nome_fantasia' => 'Distribuidora Gelada', 'tipo_loja' => 'distribuidor'],
            ['nome_fantasia' => 'Beer Garden', 'tipo_loja' => 'cervejaria'],
            ['nome_fantasia' => 'Adega Premium', 'tipo_loja' => 'distribuidor'],
            ['nome_fantasia' => 'Puro Malte Shop', 'tipo_loja' => 'cervejaria'],
        ];

        $users = User::orderBy('created_at')->limit(12)->get();

        $cnpjsGerados = [];

        foreach ($lojas as $index => $lojaData) {
            $loja = Loja::create(array_merge($lojaData, [
                'cnpj' => $this->gerarCnpjUnico($cnpjsGerados),
                'latitude' => '-23.5505',
                'longitude' => '-46.6333',
                'raio_entrega_km' => 10,
                'tempo_entrega_min' => 30,
                'tempo_entrega_max' => 60,
                'aceite_automatico' => true,
                'pedido_minimo' => 50.00,
                'taxa_comissao' => 15.00,
                'ativo' => true,
                'cep' => '01001-001',
                'logradouro' => 'Rua Exemplo',
                'numero' => $index + 1,
                'bairro' => 'Centro',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
            ]));

            // Link to user if exists (skip first two: admin and generic user)
            if (isset($users[$index + 2])) {
                $users[$index + 2]->update(['loja_id' => $loja->id]);
            }
        }
    }

    /**
     * Gera um CNPJ válido e ainda não utilizado, formatado como XX.XXX.XXX/XXXX-XX.
     *
     * @param  array<int, string>  $cnpjsGerados
     */
    private function gerarCnpjUnico(array &$cnpjsGerados): string
    {
        do {
            $cnpj = $this->gerarCnpj();
        } while (in_array($cnpj, $cnpjsGerados, true));

        $cnpjsGerados[] = $cnpj;

        return $cnpj;
    }

    private function gerarCnpj(): string
    {
        $base = [];

        for ($i = 0; $i < 8; $i++) {
            $base[] = random_int(0, 9);
        }

        // Filial fixa (0001)
        array_push($base, 0, 0, 0, 1);

        $base[] = $this->calcularDigitoVerificadorCnpj($base, [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);
        $base[] = $this->calcularDigitoVerificadorCnpj($base, [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);

        $numeros = implode('', $base);

        return sprintf(
            '%s.%s.%s/%s-%s',
            substr($numeros, 0, 2),
            substr($numeros, 2, 3),
            substr($numeros, 5, 3),
            substr($numeros, 8, 4),
            substr($numeros, 12, 2),
        );
    }

    /**
     * @param  array<int, int>  $numeros
     * @param  array<int, int>  $pesos
     */
    private function calcularDigitoVerificadorCnpj(array $numeros, array $pesos): int
    {
        $soma = 0;

        foreach ($pesos as $index => $peso) {
            $soma += $numeros[$index] * $peso;
        }

        $resto = $soma % 11;

        return $resto < 2 ? 0 : 11 - $resto;
    }
}
