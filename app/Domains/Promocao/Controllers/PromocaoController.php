<?php

namespace App\Domains\Promocao\Controllers;

use App\Domains\Shared\Controller\BaseController;
use Illuminate\Http\Request;

use App\Domains\Promocao\Services\PromocaoService;
use App\Domains\Promocao\Requests\PromocaoRequest;

class PromocaoController extends BaseController
{
    public function __construct(private readonly PromocaoService $service)
    {
        $this->setACL('promocao', [
            'list' => ['promocao.index'],
            'create' => ['promocao.store'],
            'edit'=> ['promocao.update'],
            'delete' => ['promocao.destroy']
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', PromocaoRequest::class);
    }

    // 👉 methods
    public function listarLoja(Request $request) {
		$options = $request->all();
		return $this->service->listarLoja($options);
	}
}
