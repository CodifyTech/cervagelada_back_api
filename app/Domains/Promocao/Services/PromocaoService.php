<?php

namespace App\Domains\Promocao\Services;

use App\Domains\Promocao\Models\Promocao;
use App\Domains\Shared\Services\BaseService;

class PromocaoService extends BaseService
{
    public function __construct(private readonly Promocao $promocao)
    {
        $this->setModel($this->promocao);
    }

    public function index(array $options = [], ?\Closure $builderCallback = null)
    {
        $user = auth()->user();
        if ($user && $user->loja_id) {
            $options['loja_id'] = $user->loja_id;
            return parent::index($options, function ($query) use ($user) {
                $query->where('loja_id', $user->loja_id);
            });
        }
        return parent::index($options, $builderCallback);
    }

    public function store(array $data)
    {
        $user = auth()->user();
        if ($user && $user->loja_id) {
            $data['loja_id'] = $user->loja_id;
        }

        \DB::beginTransaction();
        try {
            $promocao = parent::store($data);

            if (!empty($data['produtos'])) {
                $this->syncProdutos($promocao, $data['produtos']);
            }

            \DB::commit();
            return $promocao->load('produtos');
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    public function update(array $data, string $id)
    {
        \DB::beginTransaction();
        try {
            $promocao = parent::update($data, $id);

            if (isset($data['produtos'])) {
                $this->syncProdutos($promocao, $data['produtos']);
            }

            \DB::commit();
            return $promocao->load('produtos');
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    public function show(string $id)
    {
        $promocao = $this->promocao->with('produtos')->findOrFail($id);
        $data = $promocao->toArray();

        $data['produtos'] = $promocao->produtos->map(function ($produto) {
            return [
                'produto_id' => $produto->id,
                'preco_promocional' => $produto->pivot->preco_promocional
            ];
        })->toArray();

        return $data;
    }

    private function syncProdutos($promocao, array $produtos)
    {
        $syncData = [];
        foreach ($produtos as $p) {
            $syncData[$p['produto_id']] = [
                'id' => (string) \Illuminate\Support\Str::ulid(),
                'preco_promocional' => $p['preco_promocional']
            ];
        }
        $promocao->produtos()->sync($syncData);
    }

    // 👉 methods
    public function listarLoja($options)
    {
        $data = \App\Domains\Loja\Models\Loja::query()->paginate($options['per_page'] ?? 15);
        return $data->items();
    }
}
