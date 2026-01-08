<?php

namespace App\Domains\Promocao\Requests;

use App\Domains\Shared\Requests\BaseFormRequest;

class PromocaoRequest extends BaseFormRequest
{
    public function base(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:150'],
            'descricao' => ['nullable', 'string'],
            'data_inicio' => ['required', 'date'],
            'data_fim' => ['required', 'date'],
            'ativo' => ['required', 'boolean'],
            'produtos' => ['nullable', 'array'],
            'produtos.*.produto_id' => ['required_with:produtos', 'exists:produtos,id'],
            'produtos.*.preco_promocional' => ['required_with:produtos', 'numeric', 'min:0'],
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
