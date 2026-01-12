<?php

namespace App\Domains\Avaliacao\Services;

use App\Domains\Avaliacao\Models\Avaliacao;
use App\Domains\Shared\Services\BaseService;

class AvaliacaoService extends BaseService
{
    public function __construct(private readonly Avaliacao $avaliacao)
    {
        $this->setModel($this->avaliacao);
    }

    /**
     * Stores a new Avaliacao.
     *
     * @param  array  $data
     * @return Avaliacao
     * @throws \Exception
     */
    public function store(array $data)
    {
        $pedido = \App\Domains\Pedido\Models\Pedido::findOrFail($data['pedido_id']);

        // Check if order is delivered
        if ($pedido->status !== 'entregue') {
            throw new \Exception('Você só pode avaliar pedidos que já foram entregues.');
        }

        // Set user_id if not provided
        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id() ?? $pedido->user_id;
        }

        // Check if user is the owner of the order
        if ($data['user_id'] != $pedido->user_id) {
            throw new \Exception('Você não tem permissão para avaliar este pedido.');
        }

        // Set loja_id if not provided
        if (!isset($data['loja_id'])) {
            $data['loja_id'] = $pedido->loja_id;
        }

        // Check if user already evaluated this order
        $exists = Avaliacao::where('pedido_id', $pedido->id)->exists();
        if ($exists) {
            throw new \Exception('Este pedido já foi avaliado.');
        }

        return parent::store($data);
    }
}
