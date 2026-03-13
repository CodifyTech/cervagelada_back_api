<?php

namespace App\Domains\Configuracao\Seeders;

use App\Domains\Configuracao\Models\Configuracao;
use Illuminate\Database\Seeder;

class ConfiguracaoSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            // Contato
            ['chave' => 'contato_email',     'valor' => 'contato@cervagelada.com.br', 'tipo' => 'string', 'grupo' => 'contato'],
            ['chave' => 'contato_telefone',  'valor' => '(11) 9999-9999',             'tipo' => 'string', 'grupo' => 'contato'],
            ['chave' => 'contato_whatsapp',  'valor' => 'https://wa.me/5511999999999', 'tipo' => 'string', 'grupo' => 'contato'],
            ['chave' => 'contato_endereco',  'valor' => 'Avenida Camilo di Lellis, 1065, Centro, Pinhais/PR', 'tipo' => 'string', 'grupo' => 'contato'],
            ['chave' => 'contato_horario',   'valor' => 'Seg. a Sex. das 8:00 às 18:00', 'tipo' => 'string', 'grupo' => 'contato'],
            // Redes Sociais
            ['chave' => 'redes_facebook',    'valor' => 'https://facebook.com/cervagelada',   'tipo' => 'string', 'grupo' => 'redes_sociais'],
            ['chave' => 'redes_instagram',   'valor' => 'https://instagram.com/cervagelada',  'tipo' => 'string', 'grupo' => 'redes_sociais'],
            ['chave' => 'redes_youtube',     'valor' => 'https://youtube.com/@cervagelada',   'tipo' => 'string', 'grupo' => 'redes_sociais'],
            ['chave' => 'redes_tiktok',      'valor' => 'https://tiktok.com/@cervagelada',    'tipo' => 'string', 'grupo' => 'redes_sociais'],
            // Geral
            ['chave' => 'site_nome',         'valor' => 'CervaGelada',                'tipo' => 'string', 'grupo' => 'geral'],
            ['chave' => 'site_slogan',       'valor' => 'Do campo, ao copo.',         'tipo' => 'string', 'grupo' => 'geral'],
        ];

        foreach ($configs as $config) {
            Configuracao::updateOrCreate(['chave' => $config['chave']], $config);
        }
    }
}
