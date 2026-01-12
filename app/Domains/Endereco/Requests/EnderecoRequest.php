<?php

namespace App\Domains\Endereco\Requests;

use App\Domains\Shared\Requests\BaseFormRequest;

class EnderecoRequest extends BaseFormRequest
{
    public function base(): array
    {
        return [
            'apelido' => ['nullable', 'string', 'max:50'],
            'cep' => ['required', 'string', 'max:10'],
            'logradouro' => ['required', 'string', 'max:150'],
            'numero' => ['required', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro' => ['required', 'string', 'max:100'],
            'cidade' => ['required', 'string', 'max:100'],
            'estado' => ['required', 'string', 'max:2'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'principal' => ['required', 'boolean'],
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
