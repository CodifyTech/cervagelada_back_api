<?php

use App\Domains\ItemPedido\Controllers\ItemPedidoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ItemPedido Domain Routes
|--------------------------------------------------------------------------
|
| Rotas para o domínio ItemPedido
|
*/

Route::group([
    'middleware' => ['auth:api'],
    'as' => 'item-pedido'
], function () {

    // ItemPedido Routes
    Route::apiResource('item-pedidos', ItemPedidoController::class);
    Route::post('item-pedidos/search', [ItemPedidoController::class, 'search']);
    
    Route::get('item-pedidos/listar/pedido', [ItemPedidoController::class, 'listarPedido']);
});
