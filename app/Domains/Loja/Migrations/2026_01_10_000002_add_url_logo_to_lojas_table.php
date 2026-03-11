<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lojas', function (Blueprint $table) {
            $table->string('url_logo')->nullable()->after('nome_fantasia');
        });
    }

    public function down(): void
    {
        Schema::table('lojas', function (Blueprint $table) {
            $table->dropColumn('url_logo');
        });
    }
};
