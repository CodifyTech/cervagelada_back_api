<?php

namespace App\Domains\Endereco\Services;

use App\Domains\Auth\Models\User;
use App\Domains\Endereco\Models\Endereco;
use App\Domains\Shared\Services\BaseService;

class EnderecoService extends BaseService
{
    public function __construct(private readonly Endereco $endereco)
    {
        $this->setModel($this->endereco);
    }

    // 👉 methods
    public function listarUser($options)
    {
        $data = User::query()->paginate($options['per_page'] ?? 15);

        return $data->items();
    }

    public function store(array $data)
    {
        $data['user_id'] = auth()->user()->id;

        return $this->endereco->create($data);
    }
}
