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
        $enderecos = [
            ['cep' => '01001-001', 'logradouro' => 'Praça da Sé',          'bairro' => 'Sé',             'cidade' => 'São Paulo',     'estado' => 'SP', 'latitude' => '-23.5505', 'longitude' => '-46.6333'],
            ['cep' => '20040-020', 'logradouro' => 'Av. Rio Branco',       'bairro' => 'Centro',         'cidade' => 'Rio de Janeiro','estado' => 'RJ', 'latitude' => '-22.9068', 'longitude' => '-43.1729'],
            ['cep' => '30130-010', 'logradouro' => 'Av. Afonso Pena',      'bairro' => 'Centro',         'cidade' => 'Belo Horizonte','estado' => 'MG', 'latitude' => '-19.9167', 'longitude' => '-43.9345'],
            ['cep' => '80010-010', 'logradouro' => 'Rua XV de Novembro',   'bairro' => 'Centro',         'cidade' => 'Curitiba',      'estado' => 'PR', 'latitude' => '-25.4284', 'longitude' => '-49.2733'],
            ['cep' => '90010-150', 'logradouro' => 'Rua dos Andradas',     'bairro' => 'Centro Histórico','cidade' => 'Porto Alegre', 'estado' => 'RS', 'latitude' => '-30.0346', 'longitude' => '-51.2177'],
            ['cep' => '40020-010', 'logradouro' => 'Av. Sete de Setembro', 'bairro' => 'Centro',         'cidade' => 'Salvador',      'estado' => 'BA', 'latitude' => '-12.9714', 'longitude' => '-38.5014'],
            ['cep' => '60160-230', 'logradouro' => 'Av. Beira Mar',        'bairro' => 'Meireles',       'cidade' => 'Fortaleza',     'estado' => 'CE', 'latitude' => '-3.7172',  'longitude' => '-38.5433'],
            ['cep' => '69010-060', 'logradouro' => 'Av. Eduardo Ribeiro',  'bairro' => 'Centro',         'cidade' => 'Manaus',        'estado' => 'AM', 'latitude' => '-3.1019',  'longitude' => '-60.0250'],
            ['cep' => '51021-350', 'logradouro' => 'Av. Boa Viagem',       'bairro' => 'Boa Viagem',     'cidade' => 'Recife',        'estado' => 'PE', 'latitude' => '-8.1122',  'longitude' => '-34.8952'],
            ['cep' => '49010-000', 'logradouro' => 'Av. Ivo do Prado',     'bairro' => 'Centro',         'cidade' => 'Aracaju',       'estado' => 'SE', 'latitude' => '-10.9472', 'longitude' => '-37.0731'],
            ['cep' => '65010-240', 'logradouro' => 'Av. Getúlio Vargas',   'bairro' => 'Centro',         'cidade' => 'São Luís',      'estado' => 'MA', 'latitude' => '-2.5297',  'longitude' => '-44.3028'],
            ['cep' => '74010-010', 'logradouro' => 'Av. Goiás',            'bairro' => 'Centro',         'cidade' => 'Goiânia',       'estado' => 'GO', 'latitude' => '-16.6869', 'longitude' => '-49.2648'],
            ['cep' => '79002-100', 'logradouro' => 'Av. Afonso Pena',      'bairro' => 'Centro',         'cidade' => 'Campo Grande',  'estado' => 'MS', 'latitude' => '-20.4697', 'longitude' => '-54.6201'],
            ['cep' => '78005-000', 'logradouro' => 'Av. Tenente Col. Duarte','bairro' => 'Goiabeiras',   'cidade' => 'Cuiabá',        'estado' => 'MT', 'latitude' => '-15.5961', 'longitude' => '-56.0967'],
            ['cep' => '58010-000', 'logradouro' => 'Av. Epitácio Pessoa',  'bairro' => 'Tambaú',         'cidade' => 'João Pessoa',   'estado' => 'PB', 'latitude' => '-7.1195',  'longitude' => '-34.8450'],
            ['cep' => '59010-000', 'logradouro' => 'Av. Deodoro',          'bairro' => 'Cidade Alta',    'cidade' => 'Natal',         'estado' => 'RN', 'latitude' => '-5.7945',  'longitude' => '-35.2110'],
            ['cep' => '57020-000', 'logradouro' => 'Rua do Comércio',      'bairro' => 'Centro',         'cidade' => 'Maceió',        'estado' => 'AL', 'latitude' => '-9.6658',  'longitude' => '-35.7350'],
            ['cep' => '64000-060', 'logradouro' => 'Rua Álvaro Mendes',    'bairro' => 'Centro',         'cidade' => 'Teresina',      'estado' => 'PI', 'latitude' => '-5.0892',  'longitude' => '-42.8019'],
            ['cep' => '76801-059', 'logradouro' => 'Av. Carlos Gomes',     'bairro' => 'Centro',         'cidade' => 'Porto Velho',   'estado' => 'RO', 'latitude' => '-8.7612',  'longitude' => '-63.9004'],
            ['cep' => '68900-090', 'logradouro' => 'Av. Janary Nunes',     'bairro' => 'Central',        'cidade' => 'Macapá',        'estado' => 'AP', 'latitude' => '0.0356',   'longitude' => '-51.0705'],
        ];

        $users = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'admin');
        })->get();

        foreach ($users as $index => $user) {
            $e = $enderecos[$index % count($enderecos)];
            Endereco::create([
                'user_id'     => $user->id,
                'apelido'     => 'Casa',
                'cep'         => $e['cep'],
                'logradouro'  => $e['logradouro'],
                'numero'      => (string) rand(10, 999),
                'complemento' => null,
                'bairro'      => $e['bairro'],
                'cidade'      => $e['cidade'],
                'estado'      => $e['estado'],
                'latitude'    => $e['latitude'],
                'longitude'   => $e['longitude'],
                'principal'   => true,
            ]);
        }
    }
}
