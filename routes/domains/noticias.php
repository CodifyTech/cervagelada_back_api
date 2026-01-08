<?php

use App\Domains\Noticias\Controllers\NoticiasController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Noticias Domain Routes
|--------------------------------------------------------------------------
|
| Rotas para o domínio Noticias
|
*/

Route::group([
    'middleware' => ['auth:api'],
    'as' => 'noticias'
], function () {

    // Noticias Routes
    Route::apiResource('noticias', NoticiasController::class);
    Route::post('noticias/search', [NoticiasController::class, 'search']);
    
});
