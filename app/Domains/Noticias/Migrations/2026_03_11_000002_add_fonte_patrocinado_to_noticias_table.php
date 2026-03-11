<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->string('fonte', 150)->nullable()->after('ativo');
            $table->string('url_fonte', 255)->nullable()->after('fonte');
            $table->boolean('patrocinado')->default(false)->after('url_fonte');
            $table->string('patrocinador', 150)->nullable()->after('patrocinado');
        });
    }

    public function down(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->dropColumn(['fonte', 'url_fonte', 'patrocinado', 'patrocinador']);
        });
    }
};
