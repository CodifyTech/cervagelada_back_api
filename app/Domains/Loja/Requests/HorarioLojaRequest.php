<?php

namespace App\Domains\Loja\Requests;

use App\Domains\Shared\Requests\BaseFormRequest;

class HorarioLojaRequest extends BaseFormRequest
{
    public function base(): array
    {
        return [
            'abertura' => ['required', 'string', 'max:10'],
            'fechamento' => ['required', 'string', 'max:10'],
            'dia_semana' => ['required', 'string', 'max:10'],
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
