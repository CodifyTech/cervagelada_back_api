<?php

namespace App\Domains\Shared\Services;

use Illuminate\Support\Facades\Http;

class CepService
{
    public function consultar(string $cep): array
    {
        $cep = preg_replace('/\D/', '', $cep);

        if (strlen($cep) !== 8) {
            return ['error' => 'CEP deve conter 8 dígitos.'];
        }

        $response = Http::timeout(5)->get("https://viacep.com.br/ws/{$cep}/json/");

        if ($response->failed()) {
            return ['error' => 'Não foi possível consultar o CEP.'];
        }

        $data = $response->json();

        if (isset($data['erro']) && $data['erro'] === true) {
            return ['error' => 'CEP não encontrado.'];
        }

        $endereco = [
            'cep' => $data['cep'] ?? $cep,
            'logradouro' => $data['logradouro'] ?? '',
            'complemento' => $data['complemento'] ?? '',
            'bairro' => $data['bairro'] ?? '',
            'cidade' => $data['localidade'] ?? '',
            'estado' => $data['uf'] ?? '',
        ];

        $coords = $this->geocode($endereco);
        $endereco['latitude'] = $coords['latitude'];
        $endereco['longitude'] = $coords['longitude'];

        return $endereco;
    }

    public function geocode(array $endereco): array
    {
        $token = config('services.mapbox.token');

        if (! $token) {
            return ['latitude' => null, 'longitude' => null];
        }

        $query = implode(', ', array_filter([
            $endereco['logradouro'],
            $endereco['bairro'],
            $endereco['cidade'],
            $endereco['estado'],
            'Brasil',
        ]));

        $response = Http::timeout(5)
            ->get('https://api.mapbox.com/search/geocode/v6/forward', [
                'q' => $query,
                'country' => 'BR',
                'limit' => 1,
                'access_token' => $token,
            ]);

        if ($response->successful()) {
            $features = $response->json('features', []);

            if (count($features) > 0) {
                $coords = $features[0]['geometry']['coordinates'] ?? null;

                if ($coords) {
                    return [
                        'latitude' => (float) $coords[1],
                        'longitude' => (float) $coords[0],
                    ];
                }
            }
        }

        return [
            'latitude' => null,
            'longitude' => null,
        ];
    }
}
