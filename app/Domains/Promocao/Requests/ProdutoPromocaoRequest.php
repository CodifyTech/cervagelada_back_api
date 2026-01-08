<?php

namespace App\Domains\Promocao\Requests;

use App\Domains\Shared\Requests\BaseFormRequest;

class ProdutoPromocaoRequest extends BaseFormRequest
{
    public function base(): array
    {
        return [
            'preco_promocional' => ['required', 'numeric'],
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
