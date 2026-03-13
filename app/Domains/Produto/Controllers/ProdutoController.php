<?php

namespace App\Domains\Produto\Controllers;

use App\Domains\Produto\Models\Produto;
use App\Domains\Produto\Requests\ProdutoRequest;
use App\Domains\Produto\Services\ProdutoService;
use App\Domains\Shared\Controller\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProdutoController extends BaseController
{
    public function __construct(private readonly ProdutoService $service)
    {
        $this->setACL('produto', [
            'list' => ['produto.index'],
            'create' => ['produto.store'],
            'edit' => ['produto.update'],
            'delete' => ['produto.destroy'],
        ]);
        parent::__construct();
        $this->setService($this->service);
        $this->setRequest('request', ProdutoRequest::class);
    }

    public function index(Request $request, ?\Closure $builderCallback = null)
    {
        return parent::index($request, $builderCallback);
    }

    public function store(Request $request)
    {
        return parent::store($request);
    }

    public function update(Request $request, string $id)
    {
        return parent::update($request, $id);
    }

    public function searchByEan(string $ean)
    {
        $produto = Produto::where('ean', $ean)->first();

        if ($produto) {
            return response()->json([
                'exists' => true,
                'data' => $produto,
            ]);
        }

        return response()->json([
            'exists' => false,
            'data' => (object) [],
        ]);
    }

    public function pendentes(Request $request): JsonResponse
    {
        $query = Produto::query()
            ->with(['aprovador'])
            ->orderBy('created_at', 'desc');

        // Filtro de Status
        $status = $request->input('status', 'pendente');
        if ($status !== 'todos') {
            $query->where('status_aprovacao', $status);
        }

        // Filtro de Busca
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('marca', 'like', "%{$search}%")
                    ->orWhere('ean', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate($request->input('per_page', 20)));
    }

    public function aprovar(string $id): JsonResponse
    {
        $produto = Produto::findOrFail($id);

        $produto->update([
            'status_aprovacao' => 'aprovado',
            'motivo_reprovacao' => null,
            'aprovado_por' => auth()->id(),
            'aprovado_em' => now(),
        ]);

        return response()->json($produto->fresh('aprovador'));
    }

    public function reprovar(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'motivo' => 'required|string|max:1000',
        ]);

        $produto = Produto::findOrFail($id);

        $produto->update([
            'status_aprovacao' => 'reprovado',
            'motivo_reprovacao' => $request->input('motivo'),
            'aprovado_por' => auth()->id(),
            'aprovado_em' => now(),
        ]);

        return response()->json($produto->fresh('aprovador'));
    }
}
