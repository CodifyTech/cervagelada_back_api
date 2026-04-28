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
    use S3FileOperations;

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

        return parent::index($options, $builderCallback);
    }

    public function store(array $data)
    {
        $data = $this->handleImageUpload($data);

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
        $data = $this->handleImageUpload($data);

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
                    'url_imagem' => $data['url_imagem'] ?? null,
                    'status_aprovacao' => 'pendente',
                ];

                $produto = $this->produto::create($productData);
            } else {
                // atualiza o produto se for cervejaria
                if ($loja->tipo_loja === 'cervejaria') {
                    $updateData = [
                        'nome' => $data['nome'] ?? $produto->nome,
                        'descricao' => $data['descricao'] ?? $produto->descricao,
                        'marca' => $data['marca'] ?? $produto->marca,
                        'teor_alcoolico' => $data['teor_alcoolico'] ?? $produto->teor_alcoolico,
                        'volume_ml' => $data['volume_ml'] ?? $produto->volume_ml,
                        'pedido_minimo' => $data['pedido_minimo'] ?? $produto->pedido_minimo,
                        'fabricante' => $data['fabricante'] ?? $produto->fabricante,
                        'ean' => $data['ean'] ?? $produto->ean,
                        'sku' => $data['sku'] ?? $produto->sku,
                        'atributos' => $data['atributos'] ?? $produto->atributos,
                        'status_aprovacao' => 'pendente',
                    ];

                    if (isset($data['url_imagem'])) {
                        $updateData['url_imagem'] = $data['url_imagem'];
                    }

                    $produto->update($updateData);
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

    /**
     * Handle image upload (both UploadedFile and Base64)
     */
    private function handleImageUpload(array $data): array
    {
        if (empty($data['url_imagem'])) {
            return $data;
        }

        $image = $data['url_imagem'];

        // If it's an UploadedFile
        if ($image instanceof UploadedFile) {
            $fileName = $this->putS3File($image, 'produtos');
            if ($fileName) {
                $data['url_imagem'] = $fileName;
            }
        }
        // If it's a Base64 string
        elseif (is_string($image) && str_starts_with($image, 'data:image')) {
            $fileName = $this->processBase64Image($image, 'produtos');
            if ($fileName) {
                $data['url_imagem'] = $fileName;
            }
        }

        return $data;
    }

    /**
     * Process base64 image like desk_iva_backend
     */
    private function processBase64Image(string $base64Data, string $path): ?string
    {
        try {
            if (! preg_match('/^data:image\/(\w+);base64,/', $base64Data, $matches)) {
                return null;
            }

            $imageType = $matches[1];
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            $imageData = base64_decode($base64Data);

            if ($imageData === false) {
                return null;
            }

            // Criar arquivo temporário
            $tempFile = tempnam(sys_get_temp_dir(), 'img_').'.'.$imageType;
            file_put_contents($tempFile, $imageData);

            // Gerar nome único para o arquivo
            $fileName = Str::uuid()->toString();

            // Usar o método do trait S3FileOperations
            $uploadedFileName = $this->putS3FileIfNotExists($tempFile, $path, $fileName);

            // Deletar arquivo temporário
            @unlink($tempFile);

            return $uploadedFileName;
        } catch (\Exception $e) {
            \Log::error('Erro ao processar imagem base64: '.$e->getMessage());

            return null;
        }
    }
}
