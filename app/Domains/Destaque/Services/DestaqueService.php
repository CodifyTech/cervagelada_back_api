<?php

namespace App\Domains\Destaque\Services;

use App\Domains\Destaque\Models\Destaque;
use App\Domains\Shared\Services\BaseService;

class DestaqueService extends BaseService
{
    public function __construct(private readonly Destaque $destaque)
    {
        $this->setModel($this->destaque);
    }
}
