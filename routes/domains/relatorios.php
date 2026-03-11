<?php

use App\Domains\Relatorios\Controllers\RelatoriosController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Relatorios Domain Routes
|--------------------------------------------------------------------------
*/

Route::group([
    'middleware' => ['auth:api'],
    'prefix' => 'relatorios',
], function () {
    Route::get('pedidos', [RelatoriosController::class, 'pedidos']);
    Route::get('produtos-mais-vendidos', [RelatoriosController::class, 'produtosMaisVendidos']);
    Route::get('sellers', [RelatoriosController::class, 'sellers']);
    Route::get('financeiro', [RelatoriosController::class, 'financeiro']);
});
