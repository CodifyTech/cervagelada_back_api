<?php

namespace App\Domains\ItemPedido\Requests;

use App\Domains\Shared\Requests\BaseFormRequest;

class ItemPedidoRequest extends BaseFormRequest
{
    public function base(): array
    {
        return [
            'quantidade_solicitada' => ['required', 'numeric'],
            'quantidade_final' => ['nullable', 'numeric'],
            'preco_unitario' => ['required', 'numeric'],
            'preco_total' => ['required', 'numeric'],
            'ajuste_preco' => ['nullable', 'numeric'],
            'observacoes' => ['nullable', 'string', 'max:255'],
            'pedido_id' => ['required', 'ulid', 'exists:pedidos,id'],
        ];
    }

    public function view(): array
    {
        return [];
    }

    public function store(): array
    {
        return [];
    }

    public function update(): array
    {
        return [];
    }

    public function destroy(): array
    {
        return [];
    }
}
