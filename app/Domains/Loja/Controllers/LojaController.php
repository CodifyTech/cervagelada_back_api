<?php

namespace App\Domains\Loja\Controllers;

use App\Domains\Shared\Controller\BaseController;
use Illuminate\Http\Request;

use App\Domains\Loja\Services\LojaService;
use App\Domains\Loja\Requests\LojaRequest;

class LojaController extends BaseController
{
    public function __construct(private readonly LojaService $service)
    {
        $this->setACL('loja', [
            'list' => ['loja.index'],
            'create' => ['loja.store'],
            'edit'=> ['loja.update'],
            'delete' => ['loja.destroy']
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', LojaRequest::class);
    }

    // 👉 methods
    
}
