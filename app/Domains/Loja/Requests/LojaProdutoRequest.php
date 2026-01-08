<?php

namespace App\Domains\Loja\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LojaProdutoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'preco' => 'numeric|min:0',
            'preco_promocional' => 'nullable|numeric|min:0',
            'estoque' => 'numeric|min:0',
            'destaque' => 'boolean',
            'ativo' => 'boolean',
        ];

        if ($this->isMethod('post')) {
            $rules['preco'] = 'required|numeric|min:0';
            $rules['estoque'] = 'required|numeric|min:0';

            $rules['produto_id'] = 'nullable|exists:produtos,id';
            $rules['nome'] = 'required_without:produto_id|string|max:150';
            $rules['ean'] = 'nullable|string|max:20';
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'preco' => 'preço',
            'preco_promocional' => 'preço promocional',
            'produto_id' => 'produto',
        ];
    }
}
