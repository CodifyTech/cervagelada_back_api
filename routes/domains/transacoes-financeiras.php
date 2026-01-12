<?php

use App\Domains\TransacoesFinanceiras\Controllers\TransacoesFinanceirasController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| TransacoesFinanceiras Domain Routes
|--------------------------------------------------------------------------
|
| Rotas para o domínio TransacoesFinanceiras
|
*/

Route::group([
    'middleware' => ['auth:api'],
    'as' => 'transacoes-financeiras'
], function () {

    // TransacoesFinanceiras Routes
    Route::apiResource('transacoes-financeiras', TransacoesFinanceirasController::class);
    Route::post('transacoes-financeiras/search', [TransacoesFinanceirasController::class, 'search']);
    
    Route::get('transacoes-financeiras/listar/loja', [TransacoesFinanceirasController::class, 'listarLoja']);
    Route::get('transacoes-financeiras/listar/pedido', [TransacoesFinanceirasController::class, 'listarPedido']);
});
