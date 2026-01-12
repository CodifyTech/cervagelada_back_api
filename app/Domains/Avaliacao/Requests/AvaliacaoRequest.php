<?php

namespace App\Domains\Avaliacao\Requests;

use App\Domains\Shared\Requests\BaseFormRequest;

class AvaliacaoRequest extends BaseFormRequest
{
    public function base(): array
    {
        return [
            'avaliacao' => ['required', 'integer', 'min:1', 'max:5'],
            'comentario' => ['nullable', 'string', 'max:1000'],
            'pedido_id' => ['required', 'ulid', 'exists:pedidos,id'],
            'user_id' => ['nullable', 'ulid', 'exists:users,id'],
            'loja_id' => ['nullable', 'ulid', 'exists:lojas,id'],
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
