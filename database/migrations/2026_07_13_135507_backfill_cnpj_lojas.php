<?php

use App\Domains\Loja\Models\Loja;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $usados = Loja::query()->whereNotNull('cnpj')->pluck('cnpj')->all();

        Loja::query()->whereNull('cnpj')->each(function (Loja $loja) use (&$usados): void {
            do {
                $cnpj = fake('pt_BR')->cnpj();
            } while (in_array($cnpj, $usados, true));

            $usados[] = $cnpj;

            $loja->update(['cnpj' => $cnpj]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Loja::query()->whereNotNull('cnpj')->update(['cnpj' => null]);
    }
};
