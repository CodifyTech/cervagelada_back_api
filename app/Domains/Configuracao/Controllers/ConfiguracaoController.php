<?php

namespace App\Domains\Configuracao\Controllers;

use App\Domains\Shared\Controller\BaseController;
use App\Domains\Configuracao\Services\ConfiguracaoService;
use App\Domains\Configuracao\Requests\ConfiguracaoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfiguracaoController extends BaseController
{
    public function __construct(private readonly ConfiguracaoService $service)
    {
        $this->setACL('configuracao', [
            'list'   => ['configuracao.index'],
            'create' => ['configuracao.store'],
            'edit'   => ['configuracao.update'],
            'delete' => ['configuracao.destroy'],
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', ConfiguracaoRequest::class);
    }

    /**
     * Bulk upsert configurations.
     * PUT /configuracoes/bulk
     */
    public function bulk(Request $request): JsonResponse
    {
        $data = $request->validate([
            'configs' => ['required', 'array'],
            'configs.*' => ['nullable'],
        ]);

        $this->service->upsert($data['configs']);

        return response()->json(['message' => 'Configurações salvas com sucesso.']);
    }
}
