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
        $produtos = [
            ['nome' => 'IPA Hoppy Dreams', 'marca' => 'Cervejaria Artesanal', 'teor_alcoolico' => 6.5, 'volume_ml' => 500],
            ['nome' => 'Pilsen Premium Gold', 'marca' => 'Cervejas do Vale', 'teor_alcoolico' => 4.8, 'volume_ml' => 355],
            ['nome' => 'Stout Imperial Dark', 'marca' => 'Black Beer Co.', 'teor_alcoolico' => 8.2, 'volume_ml' => 473],
            ['nome' => 'Lager Cristalina', 'marca' => 'Puro Malte', 'teor_alcoolico' => 4.5, 'volume_ml' => 600],
            ['nome' => 'Wheat Citrus Blast', 'marca' => 'Tropical Brews', 'teor_alcoolico' => 5.2, 'volume_ml' => 500],
            ['nome' => 'Red Ale Sunset', 'marca' => 'Cervejaria Artesanal', 'teor_alcoolico' => 5.8, 'volume_ml' => 500],
            ['nome' => 'Session IPA Light', 'marca' => 'Cervejas do Vale', 'teor_alcoolico' => 4.1, 'volume_ml' => 355],
            ['nome' => 'Porter Coffee Bean', 'marca' => 'Black Beer Co.', 'teor_alcoolico' => 6.0, 'volume_ml' => 473],
            ['nome' => 'Weiss Tradition', 'marca' => 'Puro Malte', 'teor_alcoolico' => 5.4, 'volume_ml' => 600],
            ['nome' => 'Sour Berry Smash', 'marca' => 'Tropical Brews', 'teor_alcoolico' => 4.0, 'volume_ml' => 355],
            ['nome' => 'Double IPA Thunder', 'marca' => 'Cervejaria Artesanal', 'teor_alcoolico' => 9.0, 'volume_ml' => 473],
            ['nome' => 'Blonde Ale Gentle', 'marca' => 'Cervejas do Vale', 'teor_alcoolico' => 5.0, 'volume_ml' => 500],
            ['nome' => 'Witbier Spice', 'marca' => 'Tropical Brews', 'teor_alcoolico' => 4.7, 'volume_ml' => 355],
            ['nome' => 'Bock Winter', 'marca' => 'Black Beer Co.', 'teor_alcoolico' => 6.8, 'volume_ml' => 500],
            ['nome' => 'Pale Ale Classic', 'marca' => 'Puro Malte', 'teor_alcoolico' => 5.5, 'volume_ml' => 600],
        ];

        foreach ($produtos as $p) {
            Produto::create(array_merge($p, [
                'descricao' => 'Cerveja de alta qualidade, produzida com ingredientes selecionados.',
                'pedido_minimo' => 1,
            ]));
        }
    }
}
