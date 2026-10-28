<?php

namespace App\Domains\Entrega\Controllers;

use App\Domains\Entrega\Requests\ConfirmarEntregaRequest;
use App\Domains\Entrega\Services\EntregaService;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EntregaController extends Controller
{
    public function __construct(private readonly EntregaService $service) {}

    /**
     * GET /api/entregas/resumo — contadores da tela inicial do entregador.
     */
    public function resumo(): JsonResponse
    {
        return response()->json($this->service->resumo());
    }

    /**
     * GET /api/entregas/atuais — pedidos atribuídos ao entregador ainda não concluídos.
     */
    public function atuais(): JsonResponse
    {
        return response()->json(['data' => $this->service->atuais()]);
    }

    /**
     * GET /api/entregas/em-rota — pedidos despachados aguardando confirmação por PIN.
     */
    public function emRota(): JsonResponse
    {
        return response()->json(['data' => $this->service->emRota()]);
    }

    /**
     * GET /api/entregas/historico — entregas concluídas e canceladas (paginado).
     * Aceita: status, data_inicio, data_fim, per_page.
     */
    public function historico(Request $request): JsonResponse
    {
        return response()->json($this->service->historico($request->all()));
    }

    /**
     * GET /api/entregas/{id} — detalhe de uma entrega do entregador.
     */
    public function show(string $id): JsonResponse
    {
        return response()->json($this->service->detalhe($id));
    }

    /**
     * POST /api/entregas/{id}/confirmar — confirma a entrega com o PIN do cliente.
     */
    public function confirmar(ConfirmarEntregaRequest $request, string $id): JsonResponse
    {
        try {
            return response()->json($this->service->confirmar($id, $request->validated('pin')));
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            $codigo = $e->getCode() ?: 422;

            return response()->json(['message' => $e->getMessage()], $codigo);
        }
    }
}
