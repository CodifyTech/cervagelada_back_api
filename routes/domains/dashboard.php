<?php

use App\Domains\Dashboard\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard Domain Routes
|--------------------------------------------------------------------------
|
| Rotas para o domínio Dashboard
|
*/

Route::group([
    'middleware' => ['auth:api'],
    'prefix' => 'dashboard'
], function () {
    Route::get('metricas', [DashboardController::class, 'metricas']);
    Route::get('vendas-mensais', [DashboardController::class, 'vendasMensais']);
    Route::get('pedidos-por-mes', [DashboardController::class, 'pedidosPorMes']);
    Route::get('categorias-mais-vendidas', [DashboardController::class, 'categoriasMaisVendidas']);
    Route::get('top-produtos', [DashboardController::class, 'topProdutos']);
    Route::get('pedidos-recentes', [DashboardController::class, 'pedidosRecentes']);
    Route::get('dashboard', [DashboardController::class, 'dashboard']);
});
