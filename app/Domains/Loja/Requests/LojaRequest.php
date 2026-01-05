<?php

namespace App\Domains\Loja\Requests;

use App\Domains\Shared\Requests\BaseFormRequest;

class LojaRequest extends BaseFormRequest
{
    public function base(): array
    {
        return [
            'nome_fantasia' => ['required', 'string', 'max:150'],
            'tipo_loja' => ['required', 'in:distribuidor,cervejaria'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'raio_entrega_km' => ['required', 'integer'],
            'tempo_entrega_min' => ['required', 'integer'],
            'tempo_entrega_max' => ['required', 'integer'],
            'aceite_automatico' => ['required', 'boolean'],
            'pedido_minimo' => ['required', 'numeric'],
            'taxa_comissao' => ['required', 'numeric'],
            'ativo' => ['required', 'boolean'],
            'cep' => ['required', 'string', 'max:10'],
            'rua' => ['required', 'string', 'max:150'],
            'numero' => ['required', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro' => ['required', 'string', 'max:100'],
            'cidade' => ['required', 'string', 'max:100'],
            'estado' => ['required', 'string', 'max:2'],
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
