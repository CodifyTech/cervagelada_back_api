<?php

use App\Domains\Loja\Controllers\LojaController;
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
    
});
