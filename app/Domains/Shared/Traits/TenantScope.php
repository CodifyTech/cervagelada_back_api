<?php

namespace App\Domains\Shared\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

trait TenantScope
{
    public static function bootTenantScope(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            // Prevent recursion by checking if we're already in a tenant scope operation
            if (self::isInTenantScopeRecursion()) {
                return;
            }

            self::enterTenantScopeRecursion();

            try {
                $modelClass = $builder->getModel()::class;
                $tenantModels = config('cdf.tenantModels', []);

                // Check if this model should have tenant scope applied
                if (!isset($tenantModels[$modelClass])) {
                    return;
                }

                $modelConfig = $tenantModels[$modelClass];
                if (is_array($modelConfig) && !in_array('list', $modelConfig)) {
                    return;
                } elseif (!is_array($modelConfig) && !$modelConfig) {
                    return;
                }

                // Tenta obter o tenant já resolvido pelo middleware
                $tenantId = config('cdf.active_loja_id');

                if (!$tenantId) {
                    // Fallback: Tenta resolver manualmente se o middleware não rodou (console/testes)
                    $userId = self::getCurrentUserIdSafely();
                    if ($userId) {
                        // Check if user is admin using direct database query
                        $isAdmin = self::isUserAdminDirectQuery($userId);

                        if (!$isAdmin) {
                            $tenantId = self::getUserTenantIdDirectly($userId);
                        }
                    }
                }

                if ($tenantId) {
                    $table = $builder->getModel()->getTable();
                    $tenantTable = config('cdf.tenantTable');
                    $tenantColumn = config('cdf.tenantColumn');

                    // Se for a própria tabela de tenants, usa 'id', senão usa a coluna configurada
                    $colunaId = $table === $tenantTable ? "$table.id" : "$table.$tenantColumn";
                    $builder->where($colunaId, $tenantId);
                }
            } finally {
                self::exitTenantScopeRecursion();
            }
        });

        static::creating(function ($model) {
            if (self::isInTenantScopeRecursion()) {
                return;
            }

            self::enterTenantScopeRecursion();

            try {
                $modelClass = $model::class;
                $tenantModels = config('cdf.tenantModels', []);
                $tenantTable = config('cdf.tenantTable');
                $tenantColumn = config('cdf.tenantColumn');

                if (!isset($tenantModels[$modelClass]) || $model->getTable() === $tenantTable) {
                    return;
                }

                $userId = self::getCurrentUserIdSafely();

                if ($userId) {
                    // Tenta obter o tenant já resolvido pelo middleware
                    $tenantId = config('cdf.active_loja_id');

                    if (!$tenantId) {
                        $tenantId = self::getUserTenantIdDirectly($userId);
                    }

                    if ($tenantId) {
                        $model->{$tenantColumn} = $tenantId;
                    }
                }
            } finally {
                self::exitTenantScopeRecursion();
            }
        });
    }

    /**
     * Controle de recursão para evitar loops infinitos
     */
    private static $inTenantScope = false;

    private static function isInTenantScopeRecursion(): bool
    {
        return self::$inTenantScope;
    }

    private static function enterTenantScopeRecursion(): void
    {
        self::$inTenantScope = true;
    }

    private static function exitTenantScopeRecursion(): void
    {
        self::$inTenantScope = false;
    }

    /**
     * Get current user ID without triggering Auth facade loops
     */
    private static function getCurrentUserIdSafely(): ?string
    {
        try {
            // Try to get from session first (most reliable)
            if (session()->has('auth_user_id')) {
                return session('auth_user_id');
            }

            // Try to get user from JWT token if available
            $request = request();
            if ($request && method_exists($request, 'bearerToken') && $request->bearerToken() && class_exists('\Tymon\JWTAuth\Facades\JWTAuth')) {
                try {
                    $token = $request->bearerToken();
                    $payload = \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->getPayload();
                    return $payload->get('sub');
                } catch (\Exception $e) {
                    // JWT parsing failed, continue to other methods
                }
            }

            // Fallback: try to get from auth guard without triggering model queries
            if (auth()->check()) {
                return auth()->id();
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if user is admin using direct database query
     */
    private static function isUserAdminDirectQuery(string $userId): bool
    {
        static $adminCache = [];

        if (!isset($adminCache[$userId])) {
            try {
                // Check the correct table structure for roles
                $adminCache[$userId] = DB::table('role_user')
                    ->join('roles', 'role_user.role_id', '=', 'roles.id')
                    ->where('role_user.user_id', $userId)
                    ->where(function ($query) {
                        $query->where('roles.slug', 'admin')
                              ->orWhere('roles.slug', 'admin-system');
                    })
                    ->exists();
            } catch (\Exception $e) {
                $adminCache[$userId] = false;
            }
        }

        return $adminCache[$userId];
    }

    /**
     * Get user tenant_id directly from database.
     * Supports X-Store-Id header for switching.
     */
    private static function getUserTenantIdDirectly(string $userId): ?string
    {
        static $tenantCache = [];
        $tenantColumn = config('cdf.tenantColumn');

        // Try to get X-Store-Id from header (for switching stores)
        $request = request();
        if ($request && $request->hasHeader('X-Store-Id')) {
            $storeId = $request->header('X-Store-Id');

            // Check if user is admin
            $isAdmin = self::isUserAdminDirectQuery($userId);

            if ($isAdmin) {
                // Admin can access any store provided in header
                return $storeId;
            }

            // For regular users, they should only be able to switch to their primary tenant_id
            // Or if we implement a pivot table later, we check it here.
            // Currently, checking if it matches their assigned tenant_id.
            $user = DB::table('users')
                ->select($tenantColumn, 'id')
                ->where('id', $userId)
                ->first();

            $userTenantId = $user ? ($user->{$tenantColumn} ?? $user->id) : null;

            if ($userTenantId == $storeId) {
                return $storeId;
            }

            // If it doesn't match and not admin, fallback to default or block
            return $userTenantId;
        }

        if (!isset($tenantCache[$userId])) {
            try {
                $user = DB::table('users')
                    ->select($tenantColumn, 'id')
                    ->where('id', $userId)
                    ->first();

                $tenantCache[$userId] = $user ? ($user->{$tenantColumn} ?? $user->id) : null;
            } catch (\Exception $e) {
                $tenantCache[$userId] = null;
            }
        }

        return $tenantCache[$userId];
    }
}
