<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->enum('status_aprovacao', ['pendente', 'aprovado', 'reprovado'])->default('pendente')->after('atributos');
            $table->text('motivo_reprovacao')->nullable()->after('status_aprovacao');
            $table->foreignUlid('aprovado_por')->nullable()->after('motivo_reprovacao')->constrained('users')->nullOnDelete();
            $table->dateTime('aprovado_em')->nullable()->after('aprovado_por');
        });
    }

    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropForeign(['aprovado_por']);
            $table->dropColumn(['status_aprovacao', 'motivo_reprovacao', 'aprovado_por', 'aprovado_em']);
        });
    }
};
