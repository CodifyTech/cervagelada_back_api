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
        Schema::create('item_pedidos', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->decimal('quantidade_solicitada', 10, 3);
            $table->decimal('quantidade_final', 10, 3);
            $table->decimal('preco_unitario', 10, 2);
            $table->decimal('preco_total', 10, 2);
            $table->decimal('ajuste_preco', 10, 2);
            $table->string('observacoes', 255)->nullable();
            $table->foreignUlid('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignUlid('produto_id')->constrained('produtos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_pedidos');
    }
};
