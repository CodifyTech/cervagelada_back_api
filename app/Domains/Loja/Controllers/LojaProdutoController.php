<?php

namespace App\Domains\Loja\Controllers;

use App\Domains\Shared\Controller\BaseController;
use Illuminate\Http\Request;
use App\Domains\Loja\Models\Loja;
use App\Domains\Produto\Models\Produto;
use Illuminate\Support\Facades\DB;
use App\Domains\Loja\Requests\LojaProdutoRequest;
use App\Domains\Produto\Services\ProdutoService;

class LojaProdutoController extends BaseController
{
    public function __construct(private readonly ProdutoService $produtoService)
    {
    }

    /**
     * List products of a store.
     */
    public function index(string $lojaId)
    {
        $loja = Loja::findOrFail($lojaId);
        $produtos = $loja->produtos()->orderBy('created_at', 'desc')->get(); // Customize sorting as needed

        return response()->json($produtos);
    }

    /**
     * Add or link a product to a store.
     */
    public function store(LojaProdutoRequest $request, string $lojaId)
    {
        $loja = Loja::findOrFail($lojaId);

        try {
            // Logic moved to ProdutoService as requested
            $produto = $this->produtoService->createOrUpdateForStore($request->validated(), $loja);

            return response()->json(['message' => 'Produto adicionado com sucesso.', 'produto_id' => $produto->id], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao adicionar produto.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a product in a store (pivot data).
     */
    public function update(LojaProdutoRequest $request, string $lojaId, string $produtoId)
    {
        $loja = Loja::findOrFail($lojaId);

        // Check verification: "edit only if linked"
        if (!$loja->produtos()->where('produto_id', $produtoId)->exists()) {
            return response()->json(['message' => 'Produto não vinculado a esta loja.'], 404);
        }

        $pivotData = $request->only(['preco', 'preco_promocional', 'estoque', 'destaque', 'ativo']);
        $loja->produtos()->updateExistingPivot($produtoId, $pivotData);

        return response()->json(['message' => 'Produto atualizado com sucesso.']);
    }
}
