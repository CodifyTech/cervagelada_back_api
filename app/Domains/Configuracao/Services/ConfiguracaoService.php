<?php

namespace App\Domains\Configuracao\Services;

use App\Domains\Configuracao\Models\Configuracao;
use App\Domains\Shared\Services\BaseService;
use Illuminate\Support\Facades\Cache;

class ConfiguracaoService extends BaseService
{
    private const CACHE_TTL = 300; // 5 minutes

    public function __construct(private readonly Configuracao $configuracao)
    {
        $this->setModel($this->configuracao);
    }

    public static function get(string $chave, mixed $default = null): mixed
    {
        return Cache::remember("config:{$chave}", self::CACHE_TTL, function () use ($chave, $default) {
            $config = Configuracao::where('chave', $chave)->first();
            return $config ? $config->valor : $default;
        });
    }

    public function byGrupo(string $grupo): array
    {
        return Cache::remember("config:grupo:{$grupo}", self::CACHE_TTL, function () use ($grupo) {
            return Configuracao::where('grupo', $grupo)
                ->get(['chave', 'valor', 'tipo'])
                ->keyBy('chave')
                ->map(fn ($c) => $c->valor)
                ->toArray();
        });
    }

    public function upsert(array $configs): void
    {
        foreach ($configs as $chave => $valor) {
            Configuracao::updateOrCreate(
                ['chave' => $chave],
                ['valor' => $valor]
            );
            Cache::forget("config:{$chave}");
        }
        // Invalidate group caches broadly
        Cache::flush();
    }
}
