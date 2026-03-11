<?php

namespace App\Domains\Pedido\Services;

use App\Domains\Pedido\Models\Pedido;
use App\Domains\Shared\Services\BaseService;
use App\Domains\Auditoria\Services\AuditService;

class PedidoService extends BaseService
{
    public function __construct(
        private readonly Pedido $pedido,
        private AuditService $auditService = new AuditService()
    ) {
        $this->setModel($this->pedido);
    }

    /**
     * Stores a new Pedido with its items.
     *
     * @param  array  $data
     * @return Pedido
     * @throws \Exception
     */
    public function store(array $data)
    {
        return \DB::transaction(function () use ($data) {
            $itens = $data['itens'] ?? [];
            unset($data['itens']);

            $lojaId = $data['loja_id'];

            // Set user_id if not provided
            if (!isset($data['user_id']) && auth()->check()) {
                $data['user_id'] = auth()->id();
            }

            // Fallback status
            if (!isset($data['status'])) {
                $data['status'] = 'pendente';
            }

            // Calculation and Stock Validation
            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($itens as $item) {
                $produtoId = $item['produto_id'];
                $quantidade = $item['quantidade_solicitada'];

                // Check stock in the store
                $lojaProduto = \DB::table('loja_produtos')
                    ->where('loja_id', $lojaId)
                    ->where('produto_id', $produtoId)
                    ->first();

                if (!$lojaProduto) {
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
                    'id' => \Illuminate\Support\Str::ulid(),
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

            /** @var Pedido $pedido */
            $pedido = $this->model->create($data);

            foreach ($itemsToCreate as $itemData) {
                $pedido->itemPedidos()->create($itemData);
            }

            return $pedido->load('itemPedidos');
        });
    }

    /**
     * Updates an existing Pedido and handles status transitions.
     *
     * @param  array  $data
     * @param  string  $id
     * @return Pedido
     */
    public function update(array $data, string $id)
    {
        return \DB::transaction(function () use ($data, $id) {
            $pedido = $this->findById($id);
            $oldStatus = $pedido->status;
            $newStatus = $data['status'] ?? $oldStatus;

            // If cancelling an order that wasn't cancelled before, return stock
            if ($newStatus === 'cancelado' && $oldStatus !== 'cancelado') {
                foreach ($pedido->itemPedidos as $item) {
                    \DB::table('loja_produtos')
                        ->where('loja_id', $pedido->loja_id)
                        ->where('produto_id', $item->produto_id)
                        ->increment('estoque', $item->quantidade_final);
                }
            }

            $pedido->update($data);

            if ($oldStatus !== $newStatus) {
                $this->auditService->logOrderStatusChanged($pedido->id, $oldStatus, $newStatus);
            }

            return $pedido->refresh();
        });
    }

    /**
     * Returns a single Pedido with all relations needed for the detail screen.
     *
     * @param  string  $id
     * @return Pedido
     */
    public function show(string $id)
    {
        return Pedido::with([
            'itemPedidos.produto',
            'user',
            'loja',
            'pagamento',
        ])->findOrFail($id);
    }

    /**
     * Returns paginated orders for the authenticated user's store.
     * Supports filtering by status, date range, and customer name search.
     *
     * @param  array  $options
     * @return array
     */
    public function listarLoja(array $options = []): array
    {
        $lojaId = $this->resolveLojaId();

        $query = Pedido::with(['user', 'itemPedidos', 'pagamento'])
            ->where('loja_id', $lojaId);

        // Filter by status
        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }

        // Filter by date range
        if (!empty($options['data_inicio'])) {
            $query->whereDate('created_at', '>=', $options['data_inicio']);
        }
        if (!empty($options['data_fim'])) {
            $query->whereDate('created_at', '<=', $options['data_fim']);
        }

        // Search by customer name
        if (!empty($options['search'])) {
            $search = $options['search'];
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        $perPage = intval($options['per_page'] ?? 15);
        $data = $query->paginate($perPage);

        return [
            'data'         => $data->items(),
            'total'        => $data->total(),
            'page'         => $data->currentPage(),
            'current_page' => $data->currentPage(),
            'last_page'    => $data->lastPage(),
        ];
    }

    /**
     * Returns order status counts (for dashboard summary cards) for the
     * authenticated user's store.
     *
     * @return array
     */
    public function resumoLoja(): array
    {
        $lojaId = $this->resolveLojaId();

        $counts = Pedido::where('loja_id', $lojaId)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statuses = ['pendente', 'preparando', 'pronto', 'em_rota', 'entregue', 'cancelado'];
        $result = [];
        foreach ($statuses as $status) {
            $result[$status] = $counts[$status] ?? 0;
        }
        $result['total'] = array_sum($result);

        return $result;
    }

    /**
     * Resolves the loja_id for the currently authenticated user.
     * Uses the loja_id stored directly on the users table.
     *
     * @return string
     * @throws \Exception
     */
    protected function resolveLojaId(): string
    {
        $user = auth()->user();

        $lojaId = $user->loja_id ?? null;

        if (!$lojaId) {
            throw new \Exception('Loja não encontrada para o usuário autenticado.', 403);
        }

        return $lojaId;
    }
}
