<?php

namespace App\Domains\Noticias\Controllers;

use App\Domains\Noticias\Models\Noticias;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicNoticiasController extends Controller
{
    /**
     * Paginated public news feed.
     * GET /api/public/noticias
     */
    public function index(Request $request): JsonResponse
    {
        $query = Noticias::where('ativo', true)
            ->orderBy('publicado_em', 'desc');

        if ($request->filled('patrocinado')) {
            $query->where('patrocinado', filter_var($request->input('patrocinado'), FILTER_VALIDATE_BOOLEAN));
        }

        $noticias = $query->paginate($request->input('per_page', 20));

        return response()->json($noticias);
    }

    /**
     * Single news item.
     * GET /api/public/noticias/{id}
     */
    public function show(string $id): JsonResponse
    {
        $noticia = Noticias::where('ativo', true)->findOrFail($id);

        return response()->json($noticia);
    }
}
