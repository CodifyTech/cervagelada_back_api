<?php

use App\Domains\ACL\Middleware\CheckPermission;
use App\Domains\Entrega\Controllers\EntregaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Entrega Domain Routes
|--------------------------------------------------------------------------
|
| Rotas do entregador autenticado: pedidos atribuídos, em rota, histórico
| e confirmação de entrega por PIN.
|
*/

Route::group([
    'middleware' => ['auth:api'],
    'prefix' => 'entregas',
], function () {
    Route::middleware(CheckPermission::class.':admin|minhas-entregas list')->group(function () {
        Route::get('resumo', [EntregaController::class, 'resumo']);
        Route::get('atuais', [EntregaController::class, 'atuais']);
        Route::get('em-rota', [EntregaController::class, 'emRota']);
        Route::get('historico', [EntregaController::class, 'historico']);
        Route::get('{id}', [EntregaController::class, 'show']);
    });

    Route::post('{id}/confirmar', [EntregaController::class, 'confirmar'])
        ->middleware(CheckPermission::class.':admin|minhas-entregas edit');
});
