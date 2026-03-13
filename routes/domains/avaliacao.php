<?php

use App\Domains\Avaliacao\Controllers\AvaliacaoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Avaliacao Domain Routes
|--------------------------------------------------------------------------
|
| Rotas para o domínio Avaliacao
|
*/

Route::group([
    'middleware' => ['auth:api'],
    'as' => 'avaliacao',
], function () {

    // Avaliacao Routes
    Route::apiResource('avaliacoes', AvaliacaoController::class);
    Route::post('avaliacoes/search', [AvaliacaoController::class, 'search']);

    Route::get('avaliacoes/listar/pedido', [AvaliacaoController::class, 'listarPedido']);
    Route::get('avaliacoes/listar/user', [AvaliacaoController::class, 'listarUser']);
    Route::get('avaliacoes/listar/loja', [AvaliacaoController::class, 'listarLoja']);
});
