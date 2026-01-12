<?php

namespace App\Domains\TransacoesFinanceiras\Requests;

use App\Domains\Shared\Requests\BaseFormRequest;

class TransacoesFinanceirasRequest extends BaseFormRequest
{
    public function base(): array
    {
        return [
            'tipo' => ['required', 'in:credito,debito,comissao'],
            'valor' => ['required', 'numeric'],
            'descricao' => ['nullable', 'string', 'max:255'],
            'liquidado' => ['required', 'boolean'],
            'liquidado_em' => ['nullable', 'date'],
            'loja_id' => ['required', 'ulid', 'exists:lojas,id'],
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
