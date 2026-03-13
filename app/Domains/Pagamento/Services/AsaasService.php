<?php

namespace App\Domains\Pagamento\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasService
{
    private string $baseUrl;

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.asaas.api_key');
        $this->baseUrl = config('services.asaas.sandbox')
            ? 'https://sandbox.asaas.com/api/v3'
            : 'https://api.asaas.com/api/v3';
    }

    /**
     * Create a charge (cobrança) on Asaas.
     *
     * @param  array  $data  [customer, billingType, value, dueDate, description, ...]
     * @return array Asaas charge response
     */
    public function createCharge(array $data): array
    {
        $response = $this->request('POST', '/payments', $data);

        Log::info('Asaas charge created', ['charge_id' => $response['id'] ?? null]);

        return $response;
    }

    /**
     * Retrieve a charge by its Asaas ID.
     */
    public function getCharge(string $chargeId): array
    {
        return $this->request('GET', "/payments/{$chargeId}");
    }

    /**
     * Cancel (delete) a charge on Asaas.
     */
    public function cancelCharge(string $chargeId): array
    {
        return $this->request('DELETE', "/payments/{$chargeId}");
    }

    /**
     * Get the PIX QR code for a charge.
     */
    public function getPixQrCode(string $chargeId): array
    {
        return $this->request('GET', "/payments/{$chargeId}/pixQrCode");
    }

    /**
     * Create or find a customer in Asaas.
     *
     * @param  array  $data  [name, cpfCnpj, email, phone, ...]
     */
    public function createCustomer(array $data): array
    {
        return $this->request('POST', '/customers', $data);
    }

    /**
     * Find customer by CPF/CNPJ.
     */
    public function findCustomerByCpfCnpj(string $cpfCnpj): ?array
    {
        $response = $this->request('GET', '/customers', ['cpfCnpj' => $cpfCnpj]);

        $customers = $response['data'] ?? [];

        return count($customers) > 0 ? $customers[0] : null;
    }

    /**
     * Execute an HTTP request to the Asaas API.
     */
    private function request(string $method, string $endpoint, array $data = []): array
    {
        $http = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->retry(3, 500)->timeout(30);

        $url = $this->baseUrl.$endpoint;

        $response = match (strtoupper($method)) {
            'GET' => $http->get($url, $data),
            'POST' => $http->post($url, $data),
            'PUT' => $http->put($url, $data),
            'DELETE' => $http->delete($url),
            default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
        };

        if ($response->failed()) {
            Log::error('Asaas API error', [
                'method' => $method,
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            $errors = $response->json('errors', []);
            $message = ! empty($errors) ? $errors[0]['description'] : 'Erro na comunicação com o gateway de pagamento';

            throw new \RuntimeException($message, $response->status());
        }

        return $response->json();
    }
}
