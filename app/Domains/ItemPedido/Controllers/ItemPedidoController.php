<?php

namespace App\Domains\ItemPedido\Controllers;

use App\Domains\ItemPedido\Requests\ItemPedidoRequest;
use App\Domains\ItemPedido\Services\ItemPedidoService;
use App\Domains\Shared\Controller\BaseController;
use Illuminate\Http\Request;

class ItemPedidoController extends BaseController
{
    public function __construct(private readonly ItemPedidoService $service)
    {
        $this->setACL('item-pedido', [
            'list' => ['item-pedido.index'],
            'create' => ['item-pedido.store'],
            'edit' => ['item-pedido.update'],
            'delete' => ['item-pedido.destroy'],
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', ItemPedidoRequest::class);
    }

    // 👉 methods
    public function listarPedido(Request $request)
    {
        $options = $request->all();

        return $this->service->listarPedido($options);
    }
}
