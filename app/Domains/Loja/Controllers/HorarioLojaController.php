<?php

namespace App\Domains\Loja\Controllers;

use App\Domains\Loja\Requests\HorarioLojaRequest;
use App\Domains\Loja\Services\HorarioLojaService;
use App\Domains\Shared\Controller\BaseController;

class HorarioLojaController extends BaseController
{
    public function __construct(private readonly HorarioLojaService $service)
    {
        $this->setACL('loja', [
            'list' => ['loja.index'],
            'create' => ['loja.store'],
            'edit' => ['loja.update'],
            'delete' => ['loja.destroy'],
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', HorarioLojaRequest::class);
    }

    // 👉 methods

}
