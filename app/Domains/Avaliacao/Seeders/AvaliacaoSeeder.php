<?php

namespace App\Domains\Avaliacao\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domains\Avaliacao\Models\Avaliacao;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Auth\Models\User;
use App\Domains\Loja\Models\Loja;


class AvaliacaoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds for Avaliacao.
     *
     * @return void
     */
    public function run(): void
    {
        $comentarios = [
            'Entrega rápida, cerveja gelada. Adorei!',
            'Produtos de ótima qualidade, entrega no prazo.',
            'Muito bom, voltarei a comprar com certeza.',
            'Atendimento excelente e entrega pontual.',
            'Cerveja chegou bem embalada e gelada.',
            'Boa variedade de produtos, recomendo.',
            'Entrega demorou um pouco, mas produto estava perfeito.',
            'Pedido correto, sem nenhuma avaria.',
            'Experiência incrível, melhor app de cerveja!',
            'Produto veio fora da temperatura ideal.',
            'Nota máxima! Superou minhas expectativas.',
            'Bom custo-benefício, preços competitivos.',
            'Entrega rápida e embalagem caprichada.',
            'Poderia ter mais opções de cervejas artesanais.',
            'Tudo certo, sem reclamações.',
        ];

        $pedidosEntregues = Pedido::where('status', 'entregue')->get();

        foreach ($pedidosEntregues as $pedido) {
            // ~70% chance of leaving a review per delivered order
            if (rand(1, 10) > 7) continue;

            Avaliacao::create([
                'pedido_id'  => $pedido->id,
                'user_id'    => $pedido->user_id,
                'loja_id'    => $pedido->loja_id,
                'avaliacao'  => rand(3, 5),
                'comentario' => $comentarios[array_rand($comentarios)],
                'created_at' => $pedido->created_at,
                'updated_at' => $pedido->created_at,
            ]);
        }
    }
}
