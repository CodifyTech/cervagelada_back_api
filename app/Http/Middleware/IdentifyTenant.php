<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            $tenantColumn = config('cdf.tenantColumn', 'loja_id');
            $tenantId = $user->{$tenantColumn};

            // Se houver o header X-Store-Id, verifica se deve usá-lo
            if ($request->hasHeader('X-Store-Id')) {
                $headerStoreId = $request->header('X-Store-Id');

                // Somente admins e admin-systems podem trocar o contexto da loja via header
                if ($user->hasRole('admin') || $user->hasRole('admin-system')) {
                    $tenantId = $headerStoreId;
                }
            }

            // Define o tenant ativo na configuração para ser acessado globalmente
            config(['cdf.active_loja_id' => $tenantId]);
        }

        return $next($request);
    }
}
