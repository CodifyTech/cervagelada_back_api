<?php

namespace App\Domains\TransacoesFinanceiras\Services;

use App\Domains\TransacoesFinanceiras\Models\TransacoesFinanceiras;
use App\Domains\Shared\Services\BaseService;

class TransacoesFinanceirasService extends BaseService
{
    public function __construct(private readonly TransacoesFinanceiras $transacoesFinanceiras)
    {
        $this->setModel($this->transacoesFinanceiras);
    }

    // 👉 methods
    public function listarLoja($options) {
		$data = \App\Domains\Loja\Models\Loja::query()->paginate($options['per_page'] ?? 15);
		return $data->items();
	}

	public function listarPedido($options) {
		$data = \App\Domains\Pedido\Models\Pedido::query()->paginate($options['per_page'] ?? 15);
		return $data->items();
	}
}
