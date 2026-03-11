<?php

namespace App\Domains\Destaque\Requests;

use App\Domains\Shared\Requests\BaseFormRequest;

class DestaqueRequest extends BaseFormRequest
{
    public function base(): array
    {
        return [
            'titulo'          => ['required', 'string', 'max:150'],
            'descricao'       => ['nullable', 'string'],
            'imagem'          => ['nullable', 'string', 'max:255'],
            'video_url'       => ['nullable', 'url', 'max:255'],
            'cta_texto'       => ['nullable', 'string', 'max:100'],
            'cta_url'         => ['nullable', 'url', 'max:255'],
            'produto_id'      => ['nullable', 'exists:produtos,id'],
            'valor_contrato'  => ['nullable', 'numeric', 'min:0'],
            'data_inicio'     => ['required', 'date'],
            'data_fim'        => ['required', 'date', 'after_or_equal:data_inicio'],
            'ativo'           => ['required', 'boolean'],
        ];
    }

    public function view(): array { return []; }
    public function store(): array { return []; }
    public function update(): array { return []; }
    public function destroy(): array { return []; }
}
