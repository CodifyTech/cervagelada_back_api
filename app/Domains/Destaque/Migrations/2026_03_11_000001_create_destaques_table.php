<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destaques', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('titulo', 150);
            $table->text('descricao')->nullable();
            $table->string('imagem', 255)->nullable();
            $table->string('video_url', 255)->nullable();
            $table->string('cta_texto', 100)->default('Saiba mais');
            $table->string('cta_url', 255)->nullable();
            $table->foreignUlid('produto_id')->nullable()->constrained('produtos')->onDelete('set null');
            $table->decimal('valor_contrato', 12, 2)->default(0);
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destaques');
    }
};
