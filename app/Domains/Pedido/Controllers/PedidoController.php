<?php

namespace App\Domains\Pedido\Controllers;

use App\Domains\Shared\Controller\BaseController;
use Illuminate\Http\Request;

use App\Domains\Pedido\Services\PedidoService;
use App\Domains\Pedido\Requests\PedidoRequest;

class PedidoController extends BaseController
{
    public function __construct(private readonly PedidoService $service)
    {
        $this->setACL('pedido', [
            'list' => ['pedido.index'],
            'create' => ['pedido.store'],
            'edit'=> ['pedido.update'],
            'delete' => ['pedido.destroy']
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', PedidoRequest::class);
    }
}
