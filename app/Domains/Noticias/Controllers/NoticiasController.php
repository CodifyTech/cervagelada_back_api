<?php

namespace App\Domains\Noticias\Controllers;

use App\Domains\Noticias\Requests\NoticiasRequest;
use App\Domains\Noticias\Services\NoticiasService;
use App\Domains\Shared\Controller\BaseController;

class NoticiasController extends BaseController
{
    public function __construct(private readonly NoticiasService $service)
    {
        $this->setACL('noticias', [
            'list' => ['noticias.index'],
            'create' => ['noticias.store'],
            'edit' => ['noticias.update'],
            'delete' => ['noticias.destroy'],
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', NoticiasRequest::class);
    }

    // 👉 methods

}
