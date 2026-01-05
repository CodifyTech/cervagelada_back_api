<?php

namespace App\Domains\Endereco\Controllers;

use App\Domains\Shared\Controller\BaseController;
use Illuminate\Http\Request;

use App\Domains\Endereco\Services\EnderecoService;
use App\Domains\Endereco\Requests\EnderecoRequest;

class EnderecoController extends BaseController
{
    public function __construct(private readonly EnderecoService $service)
    {
        $this->setACL('endereco', [
            'list' => ['endereco.index'],
            'create' => ['endereco.store'],
            'edit'=> ['endereco.update'],
            'delete' => ['endereco.destroy']
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', EnderecoRequest::class);
    }

    // 👉 methods
    public function listarUser(Request $request) {
		$options = $request->all();
		return $this->service->listarUser($options);
	}
}
