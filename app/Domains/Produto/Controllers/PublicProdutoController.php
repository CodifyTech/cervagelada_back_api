<?php

namespace App\Domains\Produto\Controllers;

use App\Domains\Loja\Models\Loja;
use App\Domains\Produto\Models\Produto;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicProdutoController extends Controller
{
    /**
     * Search products by name (without tenant scope).
     * GET /api/public/produtos/search?nome=Cerveja
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'nome' => 'required|string|min:2',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        $nome = $request->input('nome');
        $lat = $request->input('lat');
        $lng = $request->input('lng');

        $lat = $lat !== null ? (float) $lat : null;
        $lng = $lng !== null ? (float) $lng : null;
        $hasLocation = $lat !== null && $lng !== null;

        $query = Produto::withoutGlobalScopes()
            ->where('nome', 'like', '%'.$nome.'%')
            ->aprovados();

        $query->whereHas('lojas', function ($q) use ($hasLocation, $lat, $lng) {
            $q->where('lojas.ativo', true)
                ->where('loja_produtos.ativo', true)
                ->where('loja_produtos.estoque', '>', 0);

            if ($hasLocation) {
                $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(lojas.latitude)) * cos(radians(lojas.longitude) - radians(?)) + sin(radians(?)) * sin(radians(lojas.latitude))))';
                $q->whereRaw("{$haversine} <= lojas.raio_entrega_km", [$lat, $lng, $lat]);
            }
        });

        $query->with(['lojas' => function ($q) use ($hasLocation, $lat, $lng) {
            $q->where('lojas.ativo', true)
                ->where('loja_produtos.ativo', true)
                ->where('loja_produtos.estoque', '>', 0);

            if ($hasLocation) {
                $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(lojas.latitude)) * cos(radians(lojas.longitude) - radians(?)) + sin(radians(?)) * sin(radians(lojas.latitude))))';
                $q->addSelect(\DB::raw("{$haversine} AS distancia"))
                    ->addBinding([$lat, $lng, $lat], 'select')
                    ->orderByRaw("{$haversine} ASC", [$lat, $lng, $lat]);
            }
        }]);

        $produtos = $query->limit(20)->get();

        $produtos->transform(function ($produto) {
            $loja = $produto->lojas->first();
            $produto->preco = $loja ? $loja->pivot->preco : null;
            $produto->preco_promocional = $loja ? $loja->pivot->preco_promocional : null;
            $produto->loja_id = $loja ? $loja->id : null;
            unset($produto->lojas);

            return $produto;
        });

        return response()->json([
            'data' => $produtos,
        ]);
    }

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
                ->wherePivot('ativo', true),
            ])
            ->first();

        if (! $loja) {
            return response()->json([
                'message' => 'Nenhuma loja próxima possui este produto disponível.',
                'loja' => null,
            ], 200);
        }

        return response()->json([
            'loja' => $loja,
            'preco' => $loja->produtos->first()?->pivot?->preco,
        ]);
    }
}
