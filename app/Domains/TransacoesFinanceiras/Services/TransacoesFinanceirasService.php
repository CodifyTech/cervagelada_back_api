<?php

namespace App\Domains\TransacoesFinanceiras\Services;

use App\Domains\Loja\Models\Loja;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Shared\Services\BaseService;
use App\Domains\TransacoesFinanceiras\Models\TransacoesFinanceiras;

class TransacoesFinanceirasService extends BaseService
{
    public function __construct(private readonly TransacoesFinanceiras $transacoesFinanceiras)
    {
        $this->setModel($this->transacoesFinanceiras);
    }

    // 👉 methods
    public function listarLoja($options)
    {
        $data = Loja::query()->paginate($options['per_page'] ?? 15);

        return $data->items();
    }

    public function listarPedido($options)
    {
        $data = Pedido::query()->paginate($options['per_page'] ?? 15);

        return $data->items();
    }
}
