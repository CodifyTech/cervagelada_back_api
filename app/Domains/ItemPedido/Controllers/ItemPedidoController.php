<?php

namespace App\Domains\ItemPedido\Controllers;

use App\Domains\Shared\Controller\BaseController;
use Illuminate\Http\Request;

use App\Domains\ItemPedido\Services\ItemPedidoService;
use App\Domains\ItemPedido\Requests\ItemPedidoRequest;

class ItemPedidoController extends BaseController
{
    public function __construct(private readonly ItemPedidoService $service)
    {
        $this->setACL('itempedido', [
            'list' => ['itempedido.index'],
            'create' => ['itempedido.store'],
            'edit'=> ['itempedido.update'],
            'delete' => ['itempedido.destroy']
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', ItemPedidoRequest::class);
    }

    // 👉 methods
    public function listarPedido(Request $request) {
		$options = $request->all();
		return $this->service->listarPedido($options);
	}
}
