<?php

use App\Domains\Destaque\Controllers\DestaqueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Destaque Domain Routes
|--------------------------------------------------------------------------
*/

Route::group([
    'middleware' => ['auth:api'],
    'as' => 'destaque',
], function () {
    Route::apiResource('destaques', DestaqueController::class);
    Route::post('destaques/search', [DestaqueController::class, 'search']);
});
