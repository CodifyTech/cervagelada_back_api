<?php

namespace App\Domains\Entrega\Services;

use App\Domains\Pedido\Enums\OrderStatus;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Pedido\Services\PedidoService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Operações de entrega do ponto de vista do entregador autenticado.
 *
 * Todas as consultas são restritas aos pedidos cujo `entregador_id` é o
 * usuário logado, ignorando o escopo de loja (o entregador pode atuar em
 * várias lojas).
 */
class EntregaService
{
    /**
     * Status em que o pedido ainda está atribuído ao entregador e não foi concluído.
     *
     * @var array<int, string>
     */
    private const STATUS_ATUAIS = ['aceito', 'preparando', 'pronto', 'em_rota'];

    /**
     * Status que compõem o histórico do entregador.
     *
     * @var array<int, string>
     */
    private const STATUS_HISTORICO = ['entregue', 'cancelado'];

    public function __construct(private readonly PedidoService $pedidoService) {}

    /**
     * Contadores exibidos na tela inicial do entregador.
     *
     * @return array<string, int|float>
     */
    public function resumo(): array
    {
        $atuais = $this->consultaBase()->whereIn('status', self::STATUS_ATUAIS);
        $entreguesHoje = $this->consultaBase()
            ->where('status', OrderStatus::ENTREGUE->value)
            ->whereDate('pin_validado_em', today());

        return [
            'atribuidas' => (clone $atuais)->count(),
            'prontas_para_retirada' => $this->consultaBase()->where('status', OrderStatus::PRONTO->value)->count(),
            'em_rota' => $this->consultaBase()->where('status', OrderStatus::EM_ROTA->value)->count(),
            'entregues_hoje' => (clone $entreguesHoje)->count(),
            'taxas_entrega_hoje' => (float) (clone $entreguesHoje)->sum('taxa_entrega'),
            'entregues_total' => $this->consultaBase()->where('status', OrderStatus::ENTREGUE->value)->count(),
        ];
    }

    /**
     * Pedidos atribuídos ao entregador que ainda não foram concluídos.
     */
    public function atuais(): Collection
    {
        return $this->consultaBase()
            ->with($this->relacionamentos())
            ->whereIn('status', self::STATUS_ATUAIS)
            ->orderByRaw("CASE status WHEN 'em_rota' THEN 0 WHEN 'pronto' THEN 1 WHEN 'preparando' THEN 2 ELSE 3 END")
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Pedidos já despachados, aguardando a confirmação por PIN.
     */
    public function emRota(): Collection
    {
        return $this->consultaBase()
            ->with($this->relacionamentos())
            ->where('status', OrderStatus::EM_ROTA->value)
            ->orderBy('updated_at')
            ->get();
    }

    /**
     * Histórico paginado de entregas concluídas e canceladas.
     *
     * @param  array<string, mixed>  $opcoes  status, data_inicio, data_fim, per_page
     * @return array<string, mixed>
     */
    public function historico(array $opcoes = []): array
    {
        $consulta = $this->consultaBase()
            ->with($this->relacionamentos())
            ->whereIn('status', self::STATUS_HISTORICO);

        if (! empty($opcoes['status']) && in_array($opcoes['status'], self::STATUS_HISTORICO, true)) {
            $consulta->where('status', $opcoes['status']);
        }

        if (! empty($opcoes['data_inicio'])) {
            $consulta->whereDate('updated_at', '>=', $opcoes['data_inicio']);
        }

        if (! empty($opcoes['data_fim'])) {
            $consulta->whereDate('updated_at', '<=', $opcoes['data_fim']);
        }

        $porPagina = min(max((int) ($opcoes['per_page'] ?? 15), 1), 50);
        $pagina = $consulta->orderByDesc('updated_at')->paginate($porPagina);

        return [
            'data' => $pagina->items(),
            'total' => $pagina->total(),
            'page' => $pagina->currentPage(),
            'current_page' => $pagina->currentPage(),
            'last_page' => $pagina->lastPage(),
        ];
    }

    /**
     * Detalhe de um pedido atribuído ao entregador (cliente, endereço, itens).
     */
    public function detalhe(string $id): Pedido
    {
        return $this->consultaBase()
            ->with($this->relacionamentos())
            ->findOrFail($id);
    }

    /**
     * Confirma a entrega informando o PIN fornecido pelo cliente.
     *
     * @throws \Exception
     */
    public function confirmar(string $id, string $pin): Pedido
    {
        $pedido = $this->consultaBase()->findOrFail($id);

        if ($pedido->status !== OrderStatus::EM_ROTA) {
            throw new \Exception('Somente pedidos em rota podem ser confirmados.', 422);
        }

        return $this->pedidoService->validarPin($id, $pin, true);
    }

    /**
     * Consulta base: pedidos do entregador logado, sem o escopo de loja.
     */
    private function consultaBase(): Builder
    {
        return Pedido::withoutGlobalScope('tenant')
            ->where('entregador_id', auth()->id());
    }

    /**
     * Relacionamentos carregados para as telas do entregador, expondo apenas
     * os dados necessários para a entrega.
     *
     * @return array<string, \Closure>
     */
    private function relacionamentos(): array
    {
        $semEscopoLoja = fn ($consulta) => $consulta->withoutGlobalScope('tenant');

        return [
            'user' => fn ($consulta) => $semEscopoLoja($consulta)->select('id', 'name', 'telefone'),
            'endereco' => $semEscopoLoja,
            'loja' => fn ($consulta) => $semEscopoLoja($consulta)
                ->select('id', 'nome_fantasia', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'latitude', 'longitude'),
            'itemPedidos' => $semEscopoLoja,
            'itemPedidos.produto' => fn ($consulta) => $semEscopoLoja($consulta)
                ->select('id', 'nome', 'volume_ml', 'url_imagem'),
        ];
    }
}
