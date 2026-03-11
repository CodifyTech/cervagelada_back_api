<?php

namespace App\Domains\Destaque\Controllers;

use App\Domains\Destaque\Models\Destaque;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicDestaqueController extends Controller
{
    /**
     * List active sponsored highlights.
     * GET /api/public/destaques
     */
    public function index(Request $request): JsonResponse
    {
        $destaques = Destaque::ativo()
            ->vigente()
            ->with('produto:id,nome,url_imagem,marca')
            ->latest()
            ->paginate($request->input('per_page', 10));

        return response()->json($destaques);
    }

    /**
     * Show a single destaque.
     * GET /api/public/destaques/{id}
     */
    public function show(string $id): JsonResponse
    {
        $destaque = Destaque::ativo()
            ->vigente()
            ->with('produto:id,nome,url_imagem,marca,descricao')
            ->findOrFail($id);

        return response()->json($destaque);
    }
}
