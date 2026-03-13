<?php

namespace App\Domains\Auth\Seeders;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'data' => [
                    'name' => 'Admin',
                    'email' => 'admin@admin.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'admin',
            ],
            [
                'data' => [
                    'name' => 'Admin System',
                    'email' => 'system@codifytech.com.br',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'admin-system',
            ],
            [
                'data' => [
                    'name' => 'Logista Teste',
                    'email' => 'logista@cervagelada.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'logista',
            ],
            [
                'data' => [
                    'name' => 'Funcionario Teste',
                    'email' => 'funcionario@cervagelada.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'funcionario',
            ],
            [
                'data' => [
                    'name' => 'Entregador Teste',
                    'email' => 'entregador@cervagelada.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'entregador',
            ],
            [
                'data' => [
                    'name' => 'Consumidor Teste',
                    'email' => 'consumidor@cervagelada.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],

            // Consumidores adicionais
            [
                'data' => [
                    'name' => 'João Silva',
                    'email' => 'joao.silva@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Maria Santos',
                    'email' => 'maria.santos@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Pedro Oliveira',
                    'email' => 'pedro.oliveira@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Ana Costa',
                    'email' => 'ana.costa@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Carlos Pereira',
                    'email' => 'carlos.pereira@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Fernanda Lima',
                    'email' => 'fernanda.lima@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Roberto Alves',
                    'email' => 'roberto.alves@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Juliana Ferreira',
                    'email' => 'juliana.ferreira@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Lucas Ribeiro',
                    'email' => 'lucas.ribeiro@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Mariana Gomes',
                    'email' => 'mariana.gomes@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Rafael Castro',
                    'email' => 'rafael.castro@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Patrícia Martins',
                    'email' => 'patricia.martins@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Gustavo Rocha',
                    'email' => 'gustavo.rocha@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Camila Cardoso',
                    'email' => 'camila.cardoso@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Fernando Souza',
                    'email' => 'fernando.souza@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Carolina Almeida',
                    'email' => 'carolina.almeida@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Rodrigo Nunes',
                    'email' => 'rodrigo.nunes@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Bianca Moreira',
                    'email' => 'bianca.moreira@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Paulo Barbosa',
                    'email' => 'paulo.barbosa@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],
            [
                'data' => [
                    'name' => 'Amanda Pinto',
                    'email' => 'amanda.pinto@exemplo.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'consumidor',
            ],

            // Perfis de teste nomeados
            [
                'data' => [
                    'name' => 'Seller Teste',
                    'email' => 'seller@cervagelada.com',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                ],
                'roleName' => 'logista',
            ],
        ];

        foreach ($users as $user) {
            $adminUser = User::updateOrCreate(
                ['email' => $user['data']['email']],
                $user['data']
            );
            $adminUser->assignRole($user['roleName']);
        }
    }
}
