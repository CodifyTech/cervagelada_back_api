<?php

namespace App\Domains\Produto\Services;

use App\Domains\Loja\Models\Loja;
use App\Domains\Produto\Models\Produto;
use App\Domains\Shared\Services\BaseService;
use App\Domains\Shared\Traits\S3FileOperations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ProdutoService extends BaseService
{
    public function __construct(private readonly Produto $produto)
    {
        $this->setModel($this->produto);
    }

    public function index(array $options = [], ?\Closure $builderCallback = null)
    {
        $user = auth()->user();

        // If user belongs to a store, filter products by that store
        if ($user && $user->loja_id) {
            $loja = Loja::find($user->loja_id);
            if ($loja) {
                // Return products linked to the store, ordered by pivot creation time
                $query = $loja->produtos()
                    ->withPivot(['id', 'preco', 'preco_promocional', 'estoque', 'destaque', 'ativo'])
                    ->orderBy('loja_produtos.created_at', 'desc');

                if ($builderCallback !== null) {
                    $builderCallback($query);
                }

                $data = $query->paginate($options['per_page'] ?? 15);

                // Transform the items to include pivot data at the top level
                $items = collect($data->items())->map(function ($item) {
                    if ($item->pivot) {
                        $item->preco = $item->pivot->preco;
                        $item->preco_promocional = $item->pivot->preco_promocional;
                        $item->estoque = $item->pivot->estoque;
                        $item->destaque = (bool) $item->pivot->destaque;
                        $item->ativo = (bool) $item->pivot->ativo;
                    }

                    return $item;
                });

                return [
                    'data' => $items,
                    'total' => $data->total(),
                    'page' => $data->currentPage(),
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                ];
            }
        }

        // Default or Admin behavior
        return parent::index($options, $builderCallback);
    }

    public function store(array $data)
    {
        $user = auth()->user();
        if ($user && $user->loja_id) {
            $loja = Loja::find($user->loja_id);
            if ($loja) {
                return $this->createOrUpdateForStore($data, $loja);
            }
        }

        return parent::store($data);
    }

    public function update(array $data, string $id)
    {
        $user = auth()->user();
        if ($user && $user->loja_id) {
            $loja = Loja::find($user->loja_id);
            if ($loja) {
                // Ensure the data has the product id for createOrUpdateForStore
                $data['produto_id'] = $id;

                return $this->createOrUpdateForStore($data, $loja);
            }
        }

        return parent::update($data, $id);
    }

    public function show(string $id)
    {
        $produto = $this->findById($id);
        $user = auth()->user();

        $lojaProduct = \DB::table('loja_produtos')
            ->where('loja_id', $user->loja_id)
            ->where('produto_id', $id)
            ->first();

        if ($lojaProduct) {
            $produto->preco = $lojaProduct->preco;
            $produto->preco_promocional = $lojaProduct->preco_promocional;
            $produto->estoque = $lojaProduct->estoque;
            $produto->destaque = (bool) $lojaProduct->destaque;
            $produto->ativo = (bool) $lojaProduct->ativo;
        }

        return $produto;
    }

    use S3FileOperations;

    public function createOrUpdateForStore(array $data, Loja $loja)
    {
        \DB::beginTransaction();
        try {
            $produto = null;
            $produtoId = $data['produto_id'] ?? null;

            if ($produtoId) {
                $produto = $this->produto::find($produtoId);
            } elseif (! empty($data['ean'])) {
                $produto = $this->produto::where('ean', $data['ean'])->first();
            }

            if (! $produto) {
                if ($loja->tipo_loja !== 'cervejaria') {
                    throw new \Exception('Apenas lojas do tipo Cervejaria podem cadastrar novos produtos.');
                }

                $productData = [
                    'nome' => $data['nome'] ?? null,
                    'descricao' => $data['descricao'] ?? null,
                    'marca' => $data['marca'] ?? null,
                    'teor_alcoolico' => $data['teor_alcoolico'] ?? null,
                    'volume_ml' => $data['volume_ml'] ?? null,
                    'pedido_minimo' => $data['pedido_minimo'] ?? null,
                    'fabricante' => $data['fabricante'] ?? null,
                    'ean' => $data['ean'] ?? null,
                    'sku' => $data['sku'] ?? null,
                    'atributos' => $data['atributos'] ?? null,
                    'status_aprovacao' => 'pendente',
                ];

                $produto = $this->produto::create($productData);
            }

            if (isset($data['url_imagem']) && $data['url_imagem'] instanceof UploadedFile) {
                $fileName = $this->putS3File($data['url_imagem'], 'produtos');
                if ($fileName) {
                    $produto->url_imagem = $fileName;
                    $produto->save();
                }
            }

            $pivotData = [
                'id' => (string) Str::ulid(),
                'preco' => $data['preco'] ?? 0,
                'preco_promocional' => $data['preco_promocional'] ?? null,
                'estoque' => $data['estoque'] ?? 0,
                'destaque' => $data['destaque'] ?? false,
                'ativo' => $data['ativo'] ?? true,
            ];

            if ($loja->produtos()->where('produto_id', $produto->id)->exists()) {
                $loja->produtos()->updateExistingPivot($produto->id, $pivotData);
            } else {
                $loja->produtos()->attach($produto->id, $pivotData);
            }

            \DB::commit();

            return $produto->load(['lojas' => function ($q) use ($loja) {
                $q->where('lojas.id', $loja->id);
            }]);
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }
}
