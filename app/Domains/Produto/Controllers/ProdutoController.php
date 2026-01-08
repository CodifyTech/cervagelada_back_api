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
    public function index()
    {
        $user = auth()->user();

        // Assuming User has 'loja_id' or relation 'loja'. Wait, I removed 'loja_id' from user.
        // But User is associated with Loja via 'lojas'? Or Loja has 'usuario_id'?
        // The plan said "Remove loop-back user_id from lojas".
        // Wait, "alter_users_add_fk_loja" was mentioned in step 0 request:
        // "Table stores { ... user_id bigint [ref: > users.id] }" -> This was the STARTING point (User request said "Table stores... user_id").
        // Then Step 0 said "remova o id de usuário em lojas (isso é um erro)".
        // So Loja does NOT have user_id.
        // Does User have Loja?
        // Step 28 showed `alter_users_add_fk_loja.php`: `users` table has `loja_id`.
        // So User -> belongsTo -> Loja.

        // Checking User model to confirm.
        // If User belongs to Loja, then we filter products by that Loja.

        if ($user && $user->loja_id) {
            $loja = \App\Domains\Loja\Models\Loja::find($user->loja_id);
            if ($loja) {
                // Return products linked to the store, ordered by pivot creation time (when they were added to the store)
                return response()->json($loja->produtos()
                    ->orderBy('loja_produtos.created_at', 'desc')
                    ->paginate(request()->input('per_page', 15))
                );
            }
        }

        // If no store, default behavior (maybe empty or all, here all as parent)
        return parent::index();
    }

    /**
     * Store a newly created resource in storage.
     * Overridden to link to User's Store.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user && $user->loja_id) {
            $loja = \App\Domains\Loja\Models\Loja::find($user->loja_id);
            if ($loja) {
                // Use the new Service method
                try {
                    // Normalize request data for the service (merge validation + pivot data)
                    // The standard Request might strip Pivot fields if checks exist, but here we assume $request->all() or similar.
                    // Better to use $request->all() to catch 'preco', 'estoque' etc. which might not be in ProdutoRequest rules initially.
                    // But wait, ProdutoRequest was modified by User in Step 146.

                    $data = $request->all();
                    $produto = $this->service->createOrUpdateForStore($data, $loja);
                    return response()->json($produto, 201);
                } catch (\Exception $e) {
                    return response()->json(['message' => 'Erro ao salvar produto.', 'error' => $e->getMessage()], 500);
                }
            }
        }

        // Fallback for Admin/Non-Store user (Standard Create)
        return parent::store($request);
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
