<?php

namespace App\Domains\Configuracao\Requests;

use App\Domains\Shared\Requests\BaseFormRequest;

class ConfiguracaoRequest extends BaseFormRequest
{
    public function base(): array
    {
        return [
            'chave' => ['required', 'string', 'max:100'],
            'valor' => ['nullable'],
            'tipo'  => ['nullable', 'in:string,json,boolean'],
            'grupo' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function view(): array { return []; }
    public function store(): array { return []; }
    public function update(): array { return []; }
    public function destroy(): array { return []; }
}
