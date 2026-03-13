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
    'as' => 'pedido',
], function () {

    // Static routes MUST come before apiResource to avoid the {pedido} wildcard capturing them
    Route::get('pedidos/listar/loja', [PedidoController::class, 'listarLoja']);
    Route::get('pedidos/resumo/loja', [PedidoController::class, 'resumoLoja']);
    Route::patch('pedidos/{id}/status', [PedidoController::class, 'atualizarStatus']);
    Route::post('pedidos/{id}/validar-pin', [PedidoController::class, 'validarPin']);

    // Pedido Resource Routes
    Route::apiResource('pedidos', PedidoController::class);
    Route::post('pedidos/search', [PedidoController::class, 'search']);
});
