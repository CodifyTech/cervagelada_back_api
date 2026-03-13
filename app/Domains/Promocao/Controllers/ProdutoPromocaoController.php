<?php

namespace App\Domains\Promocao\Controllers;

use App\Domains\Promocao\Requests\ProdutoPromocaoRequest;
use App\Domains\Promocao\Services\ProdutoPromocaoService;
use App\Domains\Shared\Controller\BaseController;

class ProdutoPromocaoController extends BaseController
{
    public function __construct(private readonly ProdutoPromocaoService $service)
    {
        $this->setACL('promocao', [
            'list' => ['promocao.index'],
            'create' => ['promocao.store'],
            'edit' => ['promocao.update'],
            'delete' => ['promocao.destroy'],
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', ProdutoPromocaoRequest::class);
    }

    // 👉 methods

}
