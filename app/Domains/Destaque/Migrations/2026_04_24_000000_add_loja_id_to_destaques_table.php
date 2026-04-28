<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destaques', function (Blueprint $table) {
            if (!Schema::hasColumn('destaques', 'loja_id')) {
                $table->foreignUlid('loja_id')->nullable()->after('id')->constrained('lojas')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('destaques', function (Blueprint $table) {
            if (Schema::hasColumn('destaques', 'loja_id')) {
                $table->dropForeign(['loja_id']);
                $table->dropColumn('loja_id');
            }
        });
    }
};
