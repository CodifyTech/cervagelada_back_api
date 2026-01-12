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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('taxa_entrega', 10, 2);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pendente', 'preparando', 'pronto', 'em_rota', 'entregue', 'cancelado']);
            $table->string('codigo_rastreamento', 100)->nullable();
            $table->integer('tempo_estimado_min')->nullable();
            $table->integer('tempo_estimado_max')->nullable();
            $table->foreignUlid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUlid('loja_id')->constrained('lojas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
