<?php

use App\Domains\Produto\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Produto Domain Routes
|--------------------------------------------------------------------------
|
| Rotas para o domínio Produto
|
*/

Route::group([
    'middleware' => ['auth:api'],
    'as' => 'produto',
], function () {

    // Approval routes (must come before apiResource to avoid wildcard capture)
    Route::get('produtos/pendentes', [ProdutoController::class, 'pendentes']);
    Route::post('produtos/{id}/aprovar', [ProdutoController::class, 'aprovar']);
    Route::post('produtos/{id}/reprovar', [ProdutoController::class, 'reprovar']);

    // EAN search (must come before apiResource)
    Route::get('produtos/ean/{ean}', [ProdutoController::class, 'searchByEan']);

    // Produto Routes
    Route::apiResource('produtos', ProdutoController::class);
    Route::post('produtos/search', [ProdutoController::class, 'search']);

});
