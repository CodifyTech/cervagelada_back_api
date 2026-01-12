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
        Schema::create('transacoes_financeiras', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->enum('tipo', ['credito', 'debito', 'comissao']);
            $table->decimal('valor', 10, 2);
            $table->string('descricao', 255)->nullable();
            $table->boolean('liquidado');
            $table->dateTime('liquidado_em')->default(now())->nullable();
            $table->foreignUlid('loja_id')->constrained('lojas')->onDelete('cascade');
            $table->foreignUlid('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacoes_financeiras');
    }
};
