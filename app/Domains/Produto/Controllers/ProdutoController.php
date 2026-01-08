<?php

namespace App\Domains\Produto\Controllers;

use App\Domains\Shared\Controller\BaseController;
use Illuminate\Http\Request;

use App\Domains\Produto\Services\ProdutoService;
use App\Domains\Produto\Requests\ProdutoRequest;

class ProdutoController extends BaseController
{
    public function __construct(private readonly ProdutoService $service)
    {
        $this->setACL('produto', [
            'list' => ['produto.index'],
            'create' => ['produto.store'],
            'edit'=> ['produto.update'],
            'delete' => ['produto.destroy']
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', ProdutoRequest::class);
    }

    /**
     * Display a listing of the resource.
     * Overridden to filter by User's Store.
     */
    public function index(Request $request, ?\Closure $builderCallback = null)
    {
        return parent::index($request, $builderCallback);
    }

    /**
     * Store a newly created resource in storage.
     * Overridden to link to User's Store.
     */
    public function store(Request $request)
    {
        return parent::store($request);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return parent::update($request, $id);
    }

    /**
     * Search product by EAN.
     */
    public function searchByEan(string $ean)
    {
        $produto = \App\Domains\Produto\Models\Produto::where('ean', $ean)->first();

        if ($produto) {
            return response()->json([
                'exists' => true,
                'data' => $produto
            ]);
        }

        return response()->json([
            'exists' => false,
            'data' => (object)[]
        ]);
    }
}
