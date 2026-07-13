<?php

namespace App\Domains\Loja\Requests;

use App\Domains\Shared\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class LojaRequest extends BaseFormRequest
{
    public function base(): array
    {
        return [
            'nome_fantasia' => ['required', 'string', 'max:150'],
            'cnpj' => [
                'nullable',
                'string',
                'max:18',
                'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$|^\d{14}$/',
                Rule::unique('lojas', 'cnpj')->ignore($this->route('loja')),
            ],
            'url_logo' => ['nullable', 'image', 'max:2048'],
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
            'logradouro' => ['required', 'string', 'max:150'],
            'numero' => ['required', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro' => ['required', 'string', 'max:100'],
            'cidade' => ['required', 'string', 'max:100'],
            'estado' => ['required', 'string', 'max:2'],
            'horarios' => ['nullable', 'array'],
            'horarios.*.abertura' => ['required_with:horarios', 'string', 'max:10'],
            'horarios.*.fechamento' => ['required_with:horarios', 'string', 'max:10'],
            'horarios.*.dia_semana' => ['required_with:horarios', 'string', 'max:10'],
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
