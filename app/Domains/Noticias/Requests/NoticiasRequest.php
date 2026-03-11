<?php

namespace App\Domains\Noticias\Requests;

use App\Domains\Shared\Requests\BaseFormRequest;

class NoticiasRequest extends BaseFormRequest
{
    public function base(): array
    {
        return [
            'titulo'       => ['required', 'string', 'max:150'],
            'conteudo'     => ['required', 'string'],
            'url_imagem'   => ['nullable', 'string', 'max:255'],
            'publicado_em' => ['required', 'date'],
            'ativo'        => ['required', 'boolean'],
            'fonte'        => ['nullable', 'string', 'max:150'],
            'url_fonte'    => ['nullable', 'url', 'max:255'],
            'patrocinado'  => ['nullable', 'boolean'],
            'patrocinador' => ['nullable', 'string', 'max:150'],
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
