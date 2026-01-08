<?php

namespace App\Domains\Promocao\Services;

use App\Domains\Promocao\Models\ProdutoPromocao;
use App\Domains\Shared\Services\BaseService;

class ProdutoPromocaoService extends BaseService
{
    public function __construct(private readonly ProdutoPromocao $produtoPromocao)
    {
        $this->setModel($this->produtoPromocao);
    }

    // 👉 methods
    
}
