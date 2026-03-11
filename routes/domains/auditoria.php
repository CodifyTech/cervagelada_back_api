<?php

use App\Domains\Auditoria\Controllers\AuditoriaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auditoria Domain Routes
|--------------------------------------------------------------------------
*/

Route::group([
    'middleware' => ['auth:api'],
    'prefix' => 'audit-logs',
], function () {
    Route::get('/', [AuditoriaController::class, 'index']);
    Route::get('{id}', [AuditoriaController::class, 'show']);
});
