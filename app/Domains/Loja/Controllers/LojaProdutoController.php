<?php

namespace App\Domains\Loja\Controllers;

use App\Domains\Loja\Models\Loja;
use App\Domains\Loja\Requests\LojaProdutoRequest;
use App\Domains\Produto\Services\ProdutoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LojaProdutoController extends Controller
{
    public function __construct(private readonly ProdutoService $produtoService) {}

    /**
     * List products of a store.
     */
    public function index(Request $request, string $loja)
    {
        $lojaModel = Loja::findOrFail($loja);
        $produtos = $lojaModel->produtos()->orderBy('created_at', 'desc')->get();

        return response()->json($produtos);
    }

    /**
     * Add or link a product to a store.
     */
    public function store(LojaProdutoRequest $request, string $loja)
    {
        $lojaModel = Loja::findOrFail($loja);

        try {
            // Logic moved to ProdutoService as requested
            $produto = $this->produtoService->createOrUpdateForStore($request->validated(), $lojaModel);

            return response()->json(['message' => 'Produto adicionado com sucesso.', 'produto_id' => $produto->id], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao adicionar produto.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a product in a store (pivot data).
     */
    public function update(LojaProdutoRequest $request, string $loja, string $produto)
    {
        $lojaModel = Loja::findOrFail($loja);

        // Check verification: "edit only if linked"
        if (! $lojaModel->produtos()->where('produto_id', $produto)->exists()) {
            return response()->json(['message' => 'Produto não vinculado a esta loja.'], 404);
        }

        $pivotData = $request->only(['preco', 'preco_promocional', 'estoque', 'destaque', 'ativo']);
        $lojaModel->produtos()->updateExistingPivot($produto, $pivotData);

        return response()->json(['message' => 'Produto atualizado com sucesso.']);
    }
}
