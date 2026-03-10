<?php

namespace App\Domains\Loja\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Loja\Models\Loja;
use App\Domains\Produto\Models\Produto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LojaProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lojas = Loja::all();
        $produtos = Produto::all();

        foreach ($lojas as $loja) {
            foreach ($produtos as $produto) {
                DB::table('loja_produtos')->insert([
                    'id' => Str::ulid(),
                    'loja_id' => $loja->id,
                    'produto_id' => $produto->id,
                    'preco' => rand(15, 45) + (rand(0, 99) / 100),
                    'preco_promocional' => rand(0, 1) ? (rand(10, 14) + (rand(0, 99) / 100)) : null,
                    'estoque' => rand(10, 100),
                    'destaque' => (bool)rand(0, 1),
                    'ativo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
