<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            $tenantColumn = config('cdf.tenantColumn', 'loja_id');
            $isAdmin = DB::table('role_user')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->where('role_user.user_id', $user->id)
                ->whereIn('roles.slug', ['admin', 'admin-system'])
                ->exists();

            // Admin só deve ser escopado quando escolher loja explicitamente.
            $tenantId = $isAdmin ? null : $user->{$tenantColumn};

            // Se houver o header X-Store-Id, verifica se deve usá-lo
            if ($request->hasHeader('X-Store-Id')) {
                $headerStoreId = trim((string) $request->header('X-Store-Id'));

                // Somente admins e admin-systems podem trocar o contexto da loja via header
                if ($isAdmin && $headerStoreId !== '' && strtolower($headerStoreId) !== 'null' && strtolower($headerStoreId) !== 'undefined') {
                    $tenantId = $headerStoreId;
                }
            }

            // Admin precisa listar/buscar todas as lojas sem escopo de tenant.
            if ($isAdmin) {
                $isLojasIndex = $request->isMethod('GET') && $request->is('api/lojas');
                $isLojasSearch = $request->isMethod('POST') && $request->is('api/lojas/search');

                if ($isLojasIndex || $isLojasSearch) {
                    $tenantId = null;
                }
            }

            // Define o tenant ativo na configuração para ser acessado globalmente
            config(['cdf.active_loja_id' => $tenantId]);
        }

        return $next($request);
    }
}
