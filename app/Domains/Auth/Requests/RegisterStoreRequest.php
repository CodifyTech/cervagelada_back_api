<?php

namespace App\Domains\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // User validations
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'roles' => 'required|exists:roles,slug',
            'termos' => 'required|boolean',
            'celular' => 'nullable|string',
            'cpf' => 'nullable|string|max:14',

            // Loja validations
            'nome_fantasia' => ['required', 'string', 'max:150'],
            'tipo_loja' => ['required', 'in:distribuidor,cervejaria'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
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
}
