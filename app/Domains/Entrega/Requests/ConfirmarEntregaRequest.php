<?php

namespace App\Domains\Entrega\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmarEntregaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'pin' => ['required', 'string', 'size:6'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pin.required' => 'Informe o PIN de entrega.',
            'pin.size' => 'O PIN deve ter 6 dígitos.',
        ];
    }
}
