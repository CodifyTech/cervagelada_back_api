<?php

namespace Database\Seeders;

use App\Domains\ACL\Seeders\RolesPermissionSeeder;
use App\Domains\Auth\Seeders\AuthDomainDatabaseSeeder;
use Illuminate\Database\Seeder;
use App\Domains\Endereco\Seeders\EnderecoSeeder;
use App\Domains\Loja\Seeders\LojaSeeder;
use App\Domains\Produto\Seeders\ProdutoSeeder;
use App\Domains\Loja\Seeders\HorarioLojaSeeder;
use App\Domains\Noticias\Seeders\NoticiasSeeder;
use App\Domains\Promocao\Seeders\PromocaoSeeder;
use App\Domains\Promocao\Seeders\ProdutoPromocaoSeeder;
use App\Domains\Pedido\Seeders\PedidoSeeder;
use App\Domains\ItemPedido\Seeders\ItemPedidoSeeder;
use App\Domains\Avaliacao\Seeders\AvaliacaoSeeder;
use App\Domains\TransacoesFinanceiras\Seeders\TransacoesFinanceirasSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesPermissionSeeder::class);
        $this->call(AuthDomainDatabaseSeeder::class);
        $this->call(EnderecoSeeder::class);
        $this->call(LojaSeeder::class);
        $this->call(ProdutoSeeder::class);
        $this->call(\App\Domains\Loja\Seeders\LojaProdutoSeeder::class);
        $this->call(HorarioLojaSeeder::class);
        $this->call(NoticiasSeeder::class);
        $this->call(PromocaoSeeder::class);
        $this->call(ProdutoPromocaoSeeder::class);
        $this->call(PedidoSeeder::class);
        $this->call(ItemPedidoSeeder::class);
        $this->call(AvaliacaoSeeder::class);
        $this->call(TransacoesFinanceirasSeeder::class);
    }
}
