<?php

use App\Domains\Auth\Controllers\AuthController;
use App\Domains\Configuracao\Controllers\PublicConfiguracaoController;
use App\Domains\Destaque\Controllers\PublicDestaqueController;
use App\Domains\Endereco\Controllers\PublicEnderecoController;
use App\Domains\Loja\Controllers\PublicLojaController;
use App\Domains\Noticias\Controllers\PublicNoticiasController;
use App\Domains\Pagamento\Controllers\AsaasWebhookController;
use App\Domains\Pedido\Controllers\PublicPedidoController;
use App\Domains\Produto\Controllers\PublicProdutoController;
use App\Domains\Promocao\Controllers\PublicPromocaoController;
use App\Domains\Shared\Controller\PublicCepController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
| Routes accessible without admin authentication.
| Some routes require consumer JWT auth (auth:api).
|
*/

// Webhook (no auth — validated by token header)
Route::post('webhooks/asaas', [AsaasWebhookController::class, 'handle']);

Route::group(['prefix' => 'public'], function () {

    // CEP lookup (no auth required)
    Route::get('cep/{cep}', [PublicCepController::class, 'show']);

    // Public store listing (no auth required)
    Route::get('lojas/distribuidoras', [PublicLojaController::class, 'distribuidoras']);
    Route::get('lojas/cervejarias', [PublicLojaController::class, 'cervejarias']);
    Route::get('lojas/{id}', [PublicLojaController::class, 'show']);
    Route::get('lojas/{id}/produtos', [PublicLojaController::class, 'catalogo']);

    // Public promotions
    Route::get('promocoes', [PublicPromocaoController::class, 'index']);

    // Public sponsored highlights
    Route::get('destaques', [PublicDestaqueController::class, 'index']);
    Route::get('destaques/{id}', [PublicDestaqueController::class, 'show']);

    // Nearest store for a product
    Route::get('produtos/{id}/loja-proxima', [PublicProdutoController::class, 'lojaProxima']);

    // Public news feed
    Route::get('noticias', [PublicNoticiasController::class, 'index']);
    Route::get('noticias/{id}', [PublicNoticiasController::class, 'show']);

    // Platform configurations
    Route::get('configuracoes', [PublicConfiguracaoController::class, 'index']);
    Route::get('configuracoes/{grupo}', [PublicConfiguracaoController::class, 'byGrupo']);

    // Consumer address management (requires JWT auth)
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('profile', [AuthController::class, 'updateProfile']);

        Route::get('enderecos/meus', [PublicEnderecoController::class, 'index']);
        Route::post('enderecos', [PublicEnderecoController::class, 'store']);
        Route::put('enderecos/{id}', [PublicEnderecoController::class, 'update']);
        Route::delete('enderecos/{id}', [PublicEnderecoController::class, 'destroy']);

        // Consumer order management
        Route::get('pedidos/preview', [PublicPedidoController::class, 'preview']);
        Route::get('pedidos/meus', [PublicPedidoController::class, 'meusPedidos']);
        Route::post('pedidos', [PublicPedidoController::class, 'store']);
        Route::get('pedidos/{id}', [PublicPedidoController::class, 'show']);
        Route::get('pedidos/{id}/pagamento/status', [PublicPedidoController::class, 'paymentStatus']);
    });
});
