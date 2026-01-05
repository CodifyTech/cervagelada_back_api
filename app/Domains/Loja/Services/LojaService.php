<?php

namespace App\Domains\Loja\Services;

use App\Domains\Loja\Models\Loja;
use App\Domains\Shared\Services\BaseService;

class LojaService extends BaseService
{
    public function __construct(private readonly Loja $loja)
    {
        $this->setModel($this->loja);
    }

    // 👉 methods
    
}
