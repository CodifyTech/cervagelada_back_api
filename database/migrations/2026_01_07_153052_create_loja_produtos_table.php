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
        Schema::create('loja_produtos', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('loja_id')->constrained('lojas')->onDelete('cascade');
            $table->foreignUlid('produto_id')->constrained('produtos')->onDelete('cascade');
            $table->decimal('preco', 10, 2);
            $table->decimal('preco_promocional', 10, 2)->nullable();
            $table->decimal('estoque', 10, 3)->default(0);
            $table->boolean('destaque')->default(false);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loja_produtos');
    }
};
