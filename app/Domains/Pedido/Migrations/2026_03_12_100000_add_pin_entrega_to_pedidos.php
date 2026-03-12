<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('pin_entrega', 6)->nullable()->after('endereco_id');
            $table->timestamp('pin_validado_em')->nullable()->after('pin_entrega');
            $table->unsignedTinyInteger('pin_tentativas')->default(0)->after('pin_validado_em');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['pin_entrega', 'pin_validado_em', 'pin_tentativas']);
        });
    }
};
