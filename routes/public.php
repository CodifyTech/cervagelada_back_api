<?php

use App\Domains\Shared\Controller\PublicCepController;
use App\Domains\Loja\Controllers\PublicLojaController;
use App\Domains\Endereco\Controllers\PublicEnderecoController;
use App\Domains\Pedido\Controllers\PublicPedidoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
| Routes accessible without admin authentication.
| Some routes require consumer JWT auth (auth:api).
|
*/

Route::group(['prefix' => 'public'], function () {

    // CEP lookup (no auth required)
    Route::get('cep/{cep}', [PublicCepController::class, 'show']);

    // Public store listing (no auth required)
    Route::get('lojas/distribuidoras', [PublicLojaController::class, 'distribuidoras']);
    Route::get('lojas/cervejarias', [PublicLojaController::class, 'cervejarias']);
    Route::get('lojas/{id}', [PublicLojaController::class, 'show']);
    Route::get('lojas/{id}/produtos', [PublicLojaController::class, 'catalogo']);

    // Consumer address management (requires JWT auth)
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('enderecos/meus', [PublicEnderecoController::class, 'index']);
        Route::post('enderecos', [PublicEnderecoController::class, 'store']);
        Route::put('enderecos/{id}', [PublicEnderecoController::class, 'update']);
        Route::delete('enderecos/{id}', [PublicEnderecoController::class, 'destroy']);

        // Consumer order management
        Route::get('pedidos/preview', [PublicPedidoController::class, 'preview']);
        Route::get('pedidos/meus', [PublicPedidoController::class, 'meusPedidos']);
        Route::post('pedidos', [PublicPedidoController::class, 'store']);
        Route::get('pedidos/{id}', [PublicPedidoController::class, 'show']);
    });
});
