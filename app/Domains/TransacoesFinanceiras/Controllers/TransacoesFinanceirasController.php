<?php

namespace App\Domains\TransacoesFinanceiras\Controllers;

use App\Domains\Shared\Controller\BaseController;
use App\Domains\TransacoesFinanceiras\Requests\TransacoesFinanceirasRequest;
use App\Domains\TransacoesFinanceiras\Services\TransacoesFinanceirasService;
use Illuminate\Http\Request;

class TransacoesFinanceirasController extends BaseController
{
    public function __construct(private readonly TransacoesFinanceirasService $service)
    {
        $this->setACL('transacoes-financeiras', [
            'list' => ['transacoes-financeiras.index'],
            'create' => ['transacoes-financeiras.store'],
            'edit' => ['transacoes-financeiras.update'],
            'delete' => ['transacoes-financeiras.destroy'],
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', TransacoesFinanceirasRequest::class);
    }

    // 👉 methods
    public function listarLoja(Request $request)
    {
        $options = $request->all();

        return $this->service->listarLoja($options);
    }

    public function listarPedido(Request $request)
    {
        $options = $request->all();

        return $this->service->listarPedido($options);
    }
}
