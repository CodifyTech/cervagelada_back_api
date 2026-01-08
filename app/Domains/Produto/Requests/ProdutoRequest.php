<?php

namespace App\Domains\Produto\Requests;

use App\Domains\Shared\Requests\BaseFormRequest;

class ProdutoRequest extends BaseFormRequest
{
    public function base(): array
    {
        return [
            'nome' => ['required', 'string', 'max:150'],
            'descricao' => ['nullable', 'string'],
            'marca' => ['nullable', 'string', 'max:100'],
            'teor_alcoolico' => ['nullable', 'numeric'],
            'volume_ml' => ['nullable', 'integer'],
            'url_imagem' => ['nullable'],
            'pedido_minimo' => ['nullable', 'integer'],
            'fabricante' => ['nullable', 'string', 'max:150'],
            'ean' => ['nullable', 'string', 'max:20'],
            'sku' => ['nullable', 'string', 'max:50'],
            'atributos' => ['nullable'],
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
