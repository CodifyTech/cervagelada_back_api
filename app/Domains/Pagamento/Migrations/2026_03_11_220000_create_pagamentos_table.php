<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->string('asaas_charge_id')->nullable()->index();
            $table->string('asaas_customer_id')->nullable();
            $table->enum('metodo', ['pix', 'cartao', 'boleto']);
            $table->enum('status', ['pendente', 'pago', 'recusado', 'cancelado', 'estornado'])->default('pendente');
            $table->decimal('valor', 10, 2);
            $table->timestamp('pago_em')->nullable();
            $table->json('webhook_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
