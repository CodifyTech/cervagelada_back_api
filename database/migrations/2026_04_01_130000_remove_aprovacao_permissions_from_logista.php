<?php

use App\Domains\ACL\Models\Permission;
use App\Domains\ACL\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $role = Role::where('slug', 'logista')->first();

        if ($role) {
            $permissions = Permission::where('slug', 'like', 'produto-aprovacao%')->get();

            if ($permissions->isNotEmpty()) {
                $role->permissions()->detach($permissions->pluck('id'));
            }

            // Limpar cache de permissões do papel
            Cache::forget("role_{$role->id}_permissions");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não é necessário reverter, pois as permissões serão reatribuídas pelo seeder se necessário.
    }
};
