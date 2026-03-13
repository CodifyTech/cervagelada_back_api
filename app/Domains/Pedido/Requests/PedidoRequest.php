<?php

namespace App\Domains\Pedido\Requests;

use App\Domains\Pedido\Enums\OrderStatus;
use App\Domains\Shared\Requests\BaseFormRequest;

class PedidoRequest extends BaseFormRequest
{
    public function base(): array
    {
        return [
            'subtotal' => ['nullable', 'numeric'],
            'taxa_entrega' => ['nullable', 'numeric'],
            'total' => ['nullable', 'numeric'],
            'status' => ['required', 'in:'.OrderStatus::valuesString()],
            'codigo_rastreamento' => ['nullable', 'string', 'max:100'],
            'tempo_estimado_min' => ['nullable', 'integer'],
            'tempo_estimado_max' => ['nullable', 'integer'],
            'loja_id' => ['required', 'ulid', 'exists:lojas,id'],
            'user_id' => ['nullable', 'ulid', 'exists:users,id'],
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.produto_id' => ['required', 'ulid', 'exists:produtos,id'],
            'itens.*.quantidade_solicitada' => ['required', 'numeric', 'min:0.001'],
            'itens.*.preco_unitario' => ['required', 'numeric'],
            'itens.*.observacoes' => ['nullable', 'string', 'max:255'],
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
