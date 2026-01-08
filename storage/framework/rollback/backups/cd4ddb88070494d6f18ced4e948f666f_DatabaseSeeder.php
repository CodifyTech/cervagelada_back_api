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

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesPermissionSeeder::class);
        $this->call(AuthDomainDatabaseSeeder::class);
        $this->call(EnderecoSeeder::class);
        $this->call(LojaSeeder::class);
        $this->call(ProdutoSeeder::class);
        $this->call(HorarioLojaSeeder::class);
        $this->call(NoticiasSeeder::class);
        $this->call(PromocaoSeeder::class);
        $this->call(ProdutoPromocaoSeeder::class);
    }
}
