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
        Schema::create('produto_promocoes', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('promocao_id')->constrained('promocoes')->onDelete('cascade');
            $table->foreignUlid('produto_id')->constrained('produtos')->onDelete('cascade');
            $table->decimal('preco_promocional', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_promocoes');
    }
};
