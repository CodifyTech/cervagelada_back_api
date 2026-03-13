<?php

namespace App\Domains\Destaque\Controllers;

use App\Domains\Destaque\Requests\DestaqueRequest;
use App\Domains\Destaque\Services\DestaqueService;
use App\Domains\Shared\Controller\BaseController;

class DestaqueController extends BaseController
{
    public function __construct(private readonly DestaqueService $service)
    {
        $this->setACL('destaque', [
            'list' => ['destaque.index'],
            'create' => ['destaque.store'],
            'edit' => ['destaque.update'],
            'delete' => ['destaque.destroy'],
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', DestaqueRequest::class);
    }
}
