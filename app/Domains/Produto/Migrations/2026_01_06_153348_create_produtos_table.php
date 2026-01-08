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
        Schema::create('produtos', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('nome', 150);
            $table->text('descricao')->nullable();
            $table->string('marca', 100)->nullable();
            $table->decimal('teor_alcoolico', 4, 2);
            $table->integer('volume_ml')->nullable();
            $table->string('url_imagem', 255)->nullable();
            $table->integer('pedido_minimo')->nullable();
            $table->string('fabricante', 150)->nullable();
            $table->string('ean', 20)->nullable();
            $table->string('sku', 50)->nullable();
            $table->text('atributos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
