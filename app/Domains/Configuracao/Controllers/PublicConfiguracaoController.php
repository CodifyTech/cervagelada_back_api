<?php

namespace App\Domains\Configuracao\Controllers;

use App\Domains\Configuracao\Services\ConfiguracaoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PublicConfiguracaoController extends Controller
{
    public function __construct(private readonly ConfiguracaoService $service) {}

    /**
     * Return all configs for a given group.
     * GET /api/public/configuracoes/{grupo}
     */
    public function byGrupo(string $grupo): JsonResponse
    {
        $data = $this->service->byGrupo($grupo);

        return response()->json($data);
    }

    /**
     * Return all configurations grouped.
     * GET /api/public/configuracoes
     */
    public function index(): JsonResponse
    {
        $groups = ['contato', 'redes_sociais', 'geral'];
        $result = [];
        foreach ($groups as $grupo) {
            $result[$grupo] = $this->service->byGrupo($grupo);
        }

        return response()->json($result);
    }
}
