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
    public function index(Request $request, $lojaId = null)
    {
        if (!$lojaId) {
             // Fallback or error if this was supposed to be a nested route
             return parent::index($request);
        }
        $loja = Loja::findOrFail($lojaId);
        $produtos = $loja->produtos()->orderBy('created_at', 'desc')->get(); // Customize sorting as needed

        return response()->json($produtos);
    }

    /**
     * Add or link a product to a store.
     */
    public function store(Request $request, $lojaId = null)
    {
        // Re-validate using the form request manually or rely on controller method injection if not enforcing strict compatibility.
        // But since we are here due to error, let's fix the signature.
        // We can just use the custom request if we don't extend strict BaseController or if BaseController didn't exist.
        // Assuming strict mode.
        $lojaId = $request->route('loja_id') ?? $lojaId;

        $loja = Loja::findOrFail($lojaId);

        // Manual validation since we lost the type hint injection for auto-validation (unless we use app() make)
        $validatedData = app(LojaProdutoRequest::class)->validate();

        try {
            // Logic moved to ProdutoService as requested
            $produto = $this->produtoService->createOrUpdateForStore($validatedData, $loja);

            return response()->json(['message' => 'Produto adicionado com sucesso.', 'produto_id' => $produto->id], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao adicionar produto.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a product in a store (pivot data).
     */
    public function update(Request $request, $lojaId, $produtoId = null)
    {
        // Handling the mismatch of signature vs params
        // BaseController::update(Request $request, $id) typically

        // If productID came as the 3rd arg
        if (!$produtoId) {
             // Try to guess or fail
             $produtoId = $request->route('produto_id'); // or 'produto'
        }

        $loja = Loja::findOrFail($lojaId);

        // Check verification: "edit only if linked"
        if (!$loja->produtos()->where('produto_id', $produtoId)->exists()) {
            return response()->json(['message' => 'Produto não vinculado a esta loja.'], 404);
        }

        // Manual validation if needed, or simple extraction
        $pivotData = $request->only(['preco', 'preco_promocional', 'estoque', 'destaque', 'ativo']);
        $loja->produtos()->updateExistingPivot($produtoId, $pivotData);

        return response()->json(['message' => 'Produto atualizado com sucesso.']);
    }
}
