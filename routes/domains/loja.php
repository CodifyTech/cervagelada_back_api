<?php

use App\Domains\Loja\Controllers\LojaController;
use App\Domains\Loja\Controllers\HorarioLojaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Loja Domain Routes
|--------------------------------------------------------------------------
|
| Rotas para o domínio Loja
|
*/

Route::group([
    'middleware' => ['auth:api'],
    'as' => 'loja'
], function () {

    // Loja Routes
    Route::apiResource('lojas', LojaController::class);
    Route::post('lojas/search', [LojaController::class, 'search']);

    // Loja Products
    Route::get('lojas/{loja}/produtos', [\App\Domains\Loja\Controllers\LojaProdutoController::class, 'index']);
    Route::post('lojas/{loja}/produtos', [\App\Domains\Loja\Controllers\LojaProdutoController::class, 'store']);
    Route::put('lojas/{loja}/produtos/{produto}', [\App\Domains\Loja\Controllers\LojaProdutoController::class, 'update']);


    // HorarioLoja Routes
    Route::apiResource('horario-lojas', HorarioLojaController::class);
    });
