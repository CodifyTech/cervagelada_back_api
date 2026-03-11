<?php

namespace App\Domains\Shared\Controller;

use App\Domains\Shared\Services\CepService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PublicCepController extends Controller
{
    public function __construct(private readonly CepService $cepService) {}

    public function show(string $cep): JsonResponse
    {
        $result = $this->cepService->consultar($cep);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], 422);
        }

        return response()->json($result);
    }
}
