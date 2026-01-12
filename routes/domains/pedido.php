<?php

use App\Domains\Pedido\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Pedido Domain Routes
|--------------------------------------------------------------------------
|
| Rotas para o domínio Pedido
|
*/

Route::group([
    'middleware' => ['auth:api'],
    'as' => 'pedido'
], function () {

    // Pedido Routes
    Route::apiResource('pedidos', PedidoController::class);
    Route::post('pedidos/search', [PedidoController::class, 'search']);
    
    Route::get('pedidos/listar/loja', [PedidoController::class, 'listarLoja']);
});
