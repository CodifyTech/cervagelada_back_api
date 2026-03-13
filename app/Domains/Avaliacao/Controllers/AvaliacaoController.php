<?php

namespace App\Domains\Avaliacao\Controllers;

use App\Domains\Avaliacao\Requests\AvaliacaoRequest;
use App\Domains\Avaliacao\Services\AvaliacaoService;
use App\Domains\Shared\Controller\BaseController;

class AvaliacaoController extends BaseController
{
    public function __construct(private readonly AvaliacaoService $service)
    {
        $this->setACL('avaliacao', [
            'list' => ['avaliacao.index'],
            'create' => ['avaliacao.store'],
            'edit' => ['avaliacao.update'],
            'delete' => ['avaliacao.destroy'],
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', AvaliacaoRequest::class);
    }
}
