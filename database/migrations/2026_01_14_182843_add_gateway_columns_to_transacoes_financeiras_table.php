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
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->string('gateway_id')->nullable()->after('pedido_id');
            $table->string('gateway_status')->nullable()->after('gateway_id');
            $table->string('forma_pagamento')->nullable()->after('gateway_status'); // PIX, CREDIT_CARD
            $table->text('pix_qr_code')->nullable()->after('forma_pagamento');
            $table->text('pix_qr_code_url')->nullable()->after('pix_qr_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->dropColumn(['gateway_id', 'gateway_status', 'forma_pagamento', 'pix_qr_code', 'pix_qr_code_url']);
        });
    }
};
