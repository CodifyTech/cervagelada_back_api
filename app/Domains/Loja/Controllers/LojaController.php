<?php

namespace App\Domains\Loja\Controllers;

use App\Domains\Loja\Requests\LojaRequest;
use App\Domains\Loja\Services\LojaService;
use App\Domains\Shared\Controller\BaseController;
use Illuminate\Http\Request;

class LojaController extends BaseController
{
    public function __construct(private readonly LojaService $service)
    {
        $this->setACL('loja', [
            'list' => ['loja.index'],
            'create' => ['loja.store'],
            'edit' => ['loja.update'],
            'delete' => ['loja.destroy'],
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', LojaRequest::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $loja = $this->service->createWithHorarios($request->all());

            return response()->json($loja, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao criar loja.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $loja = $this->service->updateWithHorarios($id, $request->all());

            return response()->json($loja);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar loja.', 'error' => $e->getMessage()], 500);
        }
    }
}
