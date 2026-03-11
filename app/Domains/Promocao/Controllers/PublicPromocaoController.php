<?php

namespace App\Domains\Promocao\Controllers;

use App\Domains\Promocao\Models\Promocao;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicPromocaoController extends Controller
{
    /**
     * List active promotions within date range.
     * GET /api/public/promocoes
     */
    public function index(Request $request): JsonResponse
    {
        $query = Promocao::where('ativo', true)
            ->whereDate('data_inicio', '<=', now())
            ->whereDate('data_fim', '>=', now())
            ->with([
                'loja:id,nome_fantasia,url_logo',
                'produtos:id,nome,url_imagem,marca',
            ])
            ->withOnly(['loja', 'produtos']);

        if ($request->filled('lat') && $request->filled('lng')) {
            $lat = (float) $request->input('lat');
            $lng = (float) $request->input('lng');
            $query->whereHas('loja', fn ($q) => $q->where('ativo', true)->porRaio($lat, $lng));
        }

        $promocoes = $query->latest()->paginate($request->input('per_page', 20));

        return response()->json($promocoes);
    }
}
