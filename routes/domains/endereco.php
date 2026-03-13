<?php

use App\Domains\Endereco\Controllers\EnderecoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Endereco Domain Routes
|--------------------------------------------------------------------------
|
| Rotas para o domínio Endereco
|
*/

Route::group([
    'middleware' => ['auth:api'],
    'as' => 'endereco',
], function () {

    // Endereco Routes
    Route::apiResource('enderecos', EnderecoController::class);
    Route::post('enderecos/search', [EnderecoController::class, 'search']);

    Route::get('enderecos/listar/user', [EnderecoController::class, 'listarUser']);
});
