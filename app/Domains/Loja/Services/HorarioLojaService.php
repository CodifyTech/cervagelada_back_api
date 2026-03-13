<?php

namespace App\Domains\Loja\Services;

use App\Domains\Loja\Models\HorarioLoja;
use App\Domains\Shared\Services\BaseService;

class HorarioLojaService extends BaseService
{
    public function __construct(private readonly HorarioLoja $horarioLoja)
    {
        $this->setModel($this->horarioLoja);
    }

    // 👉 methods

}
