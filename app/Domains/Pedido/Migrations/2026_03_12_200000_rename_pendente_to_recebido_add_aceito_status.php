<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Rename 'pendente' → 'recebido' on existing rows
        DB::table('pedidos')
            ->where('status', 'pendente')
            ->update(['status' => 'recebido']);

        // Step 2: Alter enum to new values (MySQL)
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN status ENUM(
            'aguardando_pagamento',
            'recebido',
            'aceito',
            'preparando',
            'pronto',
            'em_rota',
            'entregue',
            'cancelado'
        ) NOT NULL DEFAULT 'aguardando_pagamento'");
    }

    public function down(): void
    {
        DB::table('pedidos')
            ->where('status', 'recebido')
            ->update(['status' => 'pendente']);

        DB::table('pedidos')
            ->where('status', 'aceito')
            ->update(['status' => 'pendente']);

        DB::statement("ALTER TABLE pedidos MODIFY COLUMN status ENUM(
            'aguardando_pagamento',
            'pendente',
            'preparando',
            'pronto',
            'em_rota',
            'entregue',
            'cancelado'
        ) NOT NULL DEFAULT 'aguardando_pagamento'");
    }
};
