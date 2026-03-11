<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change enum to include aguardando_pagamento
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN status ENUM('aguardando_pagamento','pendente','preparando','pronto','em_rota','entregue','cancelado') DEFAULT 'aguardando_pagamento'");

        // Add endereco_id for delivery address
        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreignUlid('endereco_id')->nullable()->after('loja_id')->constrained('enderecos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['endereco_id']);
            $table->dropColumn('endereco_id');
        });

        DB::statement("ALTER TABLE pedidos MODIFY COLUMN status ENUM('pendente','preparando','pronto','em_rota','entregue','cancelado')");
    }
};
