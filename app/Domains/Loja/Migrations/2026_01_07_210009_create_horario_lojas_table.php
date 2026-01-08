<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('horario_lojas', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('loja_id')->constrained('lojas')->onDelete('cascade');
            $table->string('abertura', 10);
            $table->string('fechamento', 10);
            $table->string('dia_semana', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horario_lojas');
    }
};
