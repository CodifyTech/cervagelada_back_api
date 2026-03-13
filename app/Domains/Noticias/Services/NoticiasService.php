<?php

namespace App\Domains\Noticias\Services;

use App\Domains\Noticias\Models\Noticias;
use App\Domains\Shared\Services\BaseService;

class NoticiasService extends BaseService
{
    public function __construct(private readonly Noticias $noticias)
    {
        $this->setModel($this->noticias);
    }

    // 👉 methods

}
