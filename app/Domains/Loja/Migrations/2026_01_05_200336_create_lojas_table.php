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
        Schema::create('lojas', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('nome_fantasia', 150);
            $table->enum('tipo_loja', ['distribuidor', 'cervejaria']);
            $table->string('latitude');
            $table->string('longitude');

            $table->integer('raio_entrega_km');
            $table->integer('tempo_entrega_min');
            $table->integer('tempo_entrega_max');
            $table->boolean('aceite_automatico');
            $table->decimal('pedido_minimo', 12, 2);
            $table->decimal('taxa_comissao', 5, 2);
            $table->boolean('ativo');
            $table->string('cep', 10);
            $table->string('logradouro', 150);
            $table->string('numero', 20);
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100);
            $table->string('cidade', 100);
            $table->string('estado', 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lojas');
    }
};
