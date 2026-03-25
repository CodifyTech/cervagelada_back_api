<?php

namespace App\Domains\Loja\Controllers;

use App\Domains\Loja\Models\Loja;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicLojaController extends Controller
{
    /**
     * List distribuidoras within delivery radius.
     */
    public function distribuidoras(Request $request): JsonResponse
    {
        return $this->lojasPorTipo($request, 'distribuidor');
    }

    /**
     * List cervejarias within delivery radius.
     */
    public function cervejarias(Request $request): JsonResponse
    {
        return $this->lojasPorTipo($request, 'cervejaria');
    }

    /**
     * Show store details with horarios and stats.
     */
    public function show(string $id): JsonResponse
    {
        $loja = Loja::withoutGlobalScopes()
            ->where('ativo', true)
            ->with('horarios')
            ->withCount(['produtos' => fn ($q) => $q->where('loja_produtos.ativo', true)])
            ->withAvg('avaliacoes', 'avaliacao')
            ->findOrFail($id);

        return response()->json($loja);
    }

    /**
     * List active products of a store (catalog).
     */
    public function catalogo(Request $request, string $id): JsonResponse
    {
        $loja = Loja::withoutGlobalScopes()
            ->where('ativo', true)
            ->findOrFail($id);

        $query = $loja->produtos()
            ->where('produtos.status_aprovacao', 'aprovado')
            ->wherePivot('ativo', true)
            ->wherePivot('estoque', '>', 0);

        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $query->where('nome', 'like', "%{$busca}%");
        }

        $sortBy = $request->input('sort', 'nome');
        $sortDir = $request->input('dir', 'asc');
        $allowedSorts = ['nome', 'preco', 'destaque', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $sortColumn = in_array($sortBy, ['preco', 'destaque']) ? "loja_produtos.{$sortBy}" : "produtos.{$sortBy}";
            $query->orderBy($sortColumn, $sortDir === 'desc' ? 'desc' : 'asc');
        }

        $produtos = $query->paginate($request->input('per_page', 20));

        return response()->json($produtos);
    }

    private function lojasPorTipo(Request $request, string $tipo): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $lat = (float) $request->input('lat');
        $lng = (float) $request->input('lng');

        $lojas = Loja::withoutGlobalScopes()
            ->where('tipo_loja', $tipo)
            ->where('ativo', true)
            ->porRaio($lat, $lng)
            ->with('horarios')
            ->paginate($request->input('per_page', 20));

        return response()->json($lojas);
    }
}
