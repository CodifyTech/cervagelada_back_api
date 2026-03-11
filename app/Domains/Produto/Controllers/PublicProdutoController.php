<?php

namespace App\Domains\Produto\Controllers;

use App\Domains\Loja\Models\Loja;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicProdutoController extends Controller
{
    /**
     * Find the nearest store that carries a given product.
     * GET /api/public/produtos/{id}/loja-proxima?lat=X&lng=Y
     */
    public function lojaProxima(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $lat = (float) $request->input('lat');
        $lng = (float) $request->input('lng');

        $loja = Loja::withoutGlobalScopes()
            ->where('ativo', true)
            ->whereHas('produtos', fn ($q) => $q
                ->where('produtos.id', $id)
                ->wherePivot('ativo', true)
                ->wherePivot('estoque', '>', 0)
            )
            ->porRaio($lat, $lng)
            ->with(['produtos' => fn ($q) => $q
                ->where('produtos.id', $id)
                ->wherePivot('ativo', true)
            ])
            ->first();

        if (!$loja) {
            return response()->json([
                'message' => 'Nenhuma loja próxima possui este produto disponível.',
                'loja'    => null,
            ], 200);
        }

        return response()->json([
            'loja'  => $loja,
            'preco' => $loja->produtos->first()?->pivot?->preco,
        ]);
    }
}
