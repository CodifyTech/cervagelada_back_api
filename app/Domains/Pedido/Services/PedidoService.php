<?php

namespace App\Domains\Pedido\Services;

use App\Domains\ACL\Enums\RoleEnum;
use App\Domains\Auditoria\Services\AuditService;
use App\Domains\Auth\Models\User;
use App\Domains\Pedido\Enums\OrderStatus;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Shared\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class PedidoService extends BaseService
{
    public function __construct(
        private readonly Pedido $pedido,
        private AuditService $auditService = new AuditService
    ) {
        $this->setModel($this->pedido);
    }

    /**
     * Stores a new Pedido with its items.
     *
     * @return Pedido
     *
     * @throws \Exception
     */
    public function store(array $data)
    {
        return \DB::transaction(function () use ($data) {
            $itens = $data['itens'] ?? [];
            unset($data['itens']);

            // Set user_id if not provided
            if (! isset($data['user_id']) && auth()->check()) {
                $data['user_id'] = auth()->id();
            }

            // Fallback status
            if (! isset($data['status'])) {
                $data['status'] = OrderStatus::AGUARDANDO_PAGAMENTO->value;
            }

            // Calculation and Stock Validation
            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($itens as $item) {
                $produtoId = $item['produto_id'];
                $quantidade = $item['quantidade_solicitada'];

                // Check stock in the store
                $lojaProduto = \DB::table('loja_produtos')
                    ->where('loja_id', $data['loja_id'])
                    ->where('produto_id', $produtoId)
                    ->first();

                if (! $lojaProduto) {
                    throw new \Exception("O produto #{$produtoId} não está disponível nesta loja.");
                }

                if ($lojaProduto->estoque < $quantidade) {
                    throw new \Exception("Estoque insuficiente para o produto #{$produtoId}. Disponível: {$lojaProduto->estoque}");
                }

                // Use the database price for security (check for promotional price)
                $preco = $lojaProduto->preco_promocional ?? $lojaProduto->preco;

                $precoTotal = $preco * $quantidade;
                $subtotal += $precoTotal;

                $itemsToCreate[] = [
                    'id' => Str::ulid(),
                    'produto_id' => $produtoId,
                    'quantidade_solicitada' => $quantidade,
                    'quantidade_final' => $quantidade,
                    'preco_unitario' => $preco,
                    'preco_total' => $precoTotal,
                    'ajuste_preco' => 0,
                    'observacoes' => $item['observacoes'] ?? null,
                ];

                // Reduce stock
                \DB::table('loja_produtos')
                    ->where('id', $lojaProduto->id)
                    ->decrement('estoque', $quantidade);
            }

            $taxaEntrega = $data['taxa_entrega'] ?? 0;
            $data['subtotal'] = $subtotal;
            $data['taxa_entrega'] = $taxaEntrega;
            $data['total'] = $subtotal + $taxaEntrega;

            // Generate unique delivery PIN
            $data['pin_entrega'] = $this->generateUniquePin();

            $pedido = $this->pedido->create($data);

            foreach ($itemsToCreate as $itemData) {
                $pedido->itemPedidos()->create($itemData);
            }

            return $pedido->load('itemPedidos');
        });
    }

    /**
     * Updates an existing Pedido and handles status transitions.
     *
     * @return Pedido
     */
    public function update(array $data, string $id)
    {
        return \DB::transaction(function () use ($data, $id) {
            $pedido = $this->findById($id);
            $oldStatus = $pedido->status;
            $newStatusValue = $data['status'] ?? $oldStatus->value;
            $newStatus = $newStatusValue instanceof OrderStatus ? $newStatusValue : OrderStatus::from($newStatusValue);

            // Validate state transition
            if ($oldStatus !== $newStatus && ! $oldStatus->canTransitionTo($newStatus)) {
                throw new \Exception(
                    "Transição inválida: '{$oldStatus->value}' → '{$newStatus->value}' não é permitida.",
                    422
                );
            }

            if ($newStatus === OrderStatus::EM_ROTA && $oldStatus !== OrderStatus::EM_ROTA) {
                $this->validarEntregadorDoDespacho($pedido, $data['entregador_id'] ?? null);
            }

            // If cancelling an order that wasn't cancelled before, return stock
            if ($newStatus === OrderStatus::CANCELADO && $oldStatus !== OrderStatus::CANCELADO) {
                foreach ($pedido->itemPedidos as $item) {
                    \DB::table('loja_produtos')
                        ->where('loja_id', $pedido->loja_id)
                        ->where('produto_id', $item->produto_id)
                        ->increment('estoque', $item->quantidade_final);
                }
            }

            $pedido->update($data);

            if ($oldStatus !== $newStatus) {
                $this->auditService->logOrderStatusChanged($pedido->id, $oldStatus->value, $newStatus->value);
            }

            return $pedido->refresh();
        });
    }

    /**
     * Entregadores que podem ser escalados para a loja: usuários com o cargo
     * entregador cuja loja principal é a informada ou que possuem vínculo ativo
     * com ela em `entregador_loja`.
     *
     * @return Collection<int, User>
     */
    public function entregadoresDisponiveis(string $lojaId): Collection
    {
        return User::withoutGlobalScope('tenant')
            ->select('id', 'name', 'telefone')
            ->whereHas('belongsToManyRoles', fn ($consulta) => $consulta->where('slug', RoleEnum::Entregador->value))
            ->where(function ($consulta) use ($lojaId) {
                $consulta->where('loja_id', $lojaId)
                    ->orWhereHas('lojasEntregador', fn ($lojas) => $lojas
                        ->withoutGlobalScope('tenant')
                        ->where('lojas.id', $lojaId)
                        ->where('entregador_loja.ativo', true));
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Garante que o despacho (pronto → em_rota) informe um entregador da loja do pedido.
     *
     * @throws \Exception
     */
    private function validarEntregadorDoDespacho(Pedido $pedido, ?string $entregadorId): void
    {
        if (! $entregadorId) {
            throw new \Exception('Informe o entregador para despachar o pedido.', 422);
        }

        if (! $this->entregadoresDisponiveis($pedido->loja_id)->contains('id', $entregadorId)) {
            throw new \Exception('O entregador informado não pertence a esta loja.', 422);
        }
    }

    /**
     * Returns a single Pedido with all relations needed for the detail screen.
     *
     * @return Pedido
     */
    public function show(string $id)
    {
        return Pedido::with([
            'itemPedidos.produto',
            'user' => fn ($q) => $q->withoutGlobalScope('tenant'),
            'entregador' => fn ($q) => $q->withoutGlobalScope('tenant')->select('id', 'name', 'telefone'),
            'loja',
            'pagamento',
        ])->findOrFail($id);
    }

    /**
     * Returns paginated orders for the authenticated user's store.
     * Supports filtering by status, date range, and customer name search.
     */
    public function listarLoja(array $options = []): array
    {
        $query = Pedido::with([
            'user' => fn ($q) => $q->withoutGlobalScope('tenant'),
            'entregador' => fn ($q) => $q->withoutGlobalScope('tenant')->select('id', 'name', 'telefone'),
            'itemPedidos.produto',
            'pagamento',
        ]);

        // Filter by status
        if (! empty($options['status'])) {
            $query->where('status', $options['status']);
        }

        // Filter by date range
        if (! empty($options['data_inicio'])) {
            $query->whereDate('created_at', '>=', $options['data_inicio']);
        }
        if (! empty($options['data_fim'])) {
            $query->whereDate('created_at', '<=', $options['data_fim']);
        }

        // Search by customer name
        if (! empty($options['search'])) {
            $search = $options['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->withoutGlobalScope('tenant')->where('name', 'like', "%{$search}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        $perPage = intval($options['per_page'] ?? 15);
        $data = $query->paginate($perPage);

        return [
            'data' => $data->items(),
            'total' => $data->total(),
            'page' => $data->currentPage(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
        ];
    }

    /**
     * Returns order status counts (for dashboard summary cards) for the
     * authenticated user's store.
     */
    public function resumoLoja(): array
    {
        $counts = Pedido::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statuses = OrderStatus::values();
        $result = [];
        foreach ($statuses as $status) {
            $result[$status] = $counts[$status] ?? 0;
        }
        $result['total'] = array_sum($result);

        return $result;
    }

    /**
     * Valida o PIN de entrega de um pedido.
     * Retorna o Pedido atualizado em caso de sucesso e lança exceção em caso de falha.
     *
     * A exceção de PIN incorreto é lançada fora da transação para que o
     * incremento de `pin_tentativas` (e o log de auditoria) não sofra rollback.
     *
     * @param  bool  $ignorarEscopoLoja  Ignora o escopo de loja (usado pelo entregador, que atua em várias lojas).
     *
     * @throws \Exception
     */
    public function validarPin(string $id, string $pin, bool $ignorarEscopoLoja = false): Pedido
    {
        $tentativasRestantes = 0;

        $pedidoEntregue = \DB::transaction(function () use ($id, $pin, $ignorarEscopoLoja, &$tentativasRestantes) {
            $consulta = Pedido::lockForUpdate();

            if ($ignorarEscopoLoja) {
                $consulta->withoutGlobalScope('tenant');
            }

            $pedido = $consulta->findOrFail($id);

            if ($pedido->pin_validado_em) {
                throw new \Exception('PIN já foi validado para este pedido.', 422);
            }

            if ($pedido->pin_tentativas >= 3) {
                throw new \Exception('Número máximo de tentativas excedido. Contate o suporte.', 429);
            }

            if (! hash_equals((string) $pedido->pin_entrega, $pin)) {
                $pedido->increment('pin_tentativas');
                $tentativasRestantes = max(0, 3 - $pedido->pin_tentativas);
                $this->auditService->log('pin_validation_failed', 'pedido', $id, null, null, [
                    'tentativa' => $pedido->pin_tentativas,
                ]);

                return null;
            }

            $statusAnterior = $pedido->status;
            $pedido->update([
                'pin_validado_em' => now(),
                'status' => OrderStatus::ENTREGUE->value,
            ]);

            $this->auditService->log('pin_validated', 'pedido', $id);
            $this->auditService->logOrderStatusChanged($id, $statusAnterior->value, OrderStatus::ENTREGUE->value);

            return $pedido->refresh();
        });

        if ($pedidoEntregue === null) {
            throw new \Exception("PIN incorreto. {$tentativasRestantes} tentativa(s) restante(s).", 422);
        }

        return $pedidoEntregue;
    }

    /**
     * Generate a unique 6-digit PIN.
     */
    private function generateUniquePin(): string
    {
        do {
            $pin = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Pedido::where('pin_entrega', $pin)->whereNotIn('status', ['entregue', 'cancelado'])->exists());

        return $pin;
    }
}
