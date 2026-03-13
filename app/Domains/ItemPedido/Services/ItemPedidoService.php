<?php

namespace App\Domains\ItemPedido\Services;

use App\Domains\ItemPedido\Models\ItemPedido;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Shared\Services\BaseService;

class ItemPedidoService extends BaseService
{
    public function __construct(private readonly ItemPedido $itemPedido)
    {
        $this->setModel($this->itemPedido);
    }

    // 👉 methods
    public function listarPedido($options)
    {
        $data = Pedido::query()->paginate($options['per_page'] ?? 15);

        return $data->items();
    }
}
