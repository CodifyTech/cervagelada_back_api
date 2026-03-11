<?php

use App\Domains\Configuracao\Controllers\ConfiguracaoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Configuracao Domain Routes
|--------------------------------------------------------------------------
*/

Route::group([
    'middleware' => ['auth:api'],
    'as' => 'configuracao',
], function () {
    Route::apiResource('configuracoes', ConfiguracaoController::class);
    Route::put('configuracoes/bulk', [ConfiguracaoController::class, 'bulk']);
});
