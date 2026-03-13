<?php

namespace App\Domains\Pedido\Services;

use App\Domains\Endereco\Models\Endereco;
use App\Domains\Loja\Models\Loja;

class DeliveryFeeService
{
    /**
     * Flat base fee (R$) + per-km rate after the first free kilometer.
     */
    private const BASE_FEE = 5.00;

    private const PER_KM_RATE = 1.50;

    private const FREE_KM = 1.0;

    /**
     * Calculate delivery fee based on Haversine distance between store and address.
     */
    public static function calculate(string $lojaId, Endereco $endereco): float
    {
        $loja = Loja::findOrFail($lojaId);

        if (! $endereco->latitude || ! $endereco->longitude || ! $loja->latitude || ! $loja->longitude) {
            return self::BASE_FEE;
        }

        $distance = self::haversineKm(
            (float) $loja->latitude,
            (float) $loja->longitude,
            (float) $endereco->latitude,
            (float) $endereco->longitude,
        );

        if ($distance <= self::FREE_KM) {
            return self::BASE_FEE;
        }

        $extraKm = $distance - self::FREE_KM;

        return round(self::BASE_FEE + ($extraKm * self::PER_KM_RATE), 2);
    }

    /**
     * Haversine formula — returns distance in kilometers.
     */
    private static function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
