<?php

namespace App\Domains\Produto\Services;

use App\Domains\Loja\Models\Loja;
use App\Domains\Produto\Models\Produto;
use App\Domains\Shared\Services\BaseService;
use App\Domains\Shared\Services\UploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ProdutoService extends BaseService
{
    public function __construct(
        private readonly Produto $produto,
        private readonly UploadService $uploadService
    ) {
        $this->setModel($this->produto);
    }

    public function index(array $options = [], ?\Closure $builderCallback = null)
    {
        $user = auth()->user();

        if ($user && $user->loja_id) {
            $loja = Loja::find($user->loja_id);
            if ($loja) {
                $query = $loja->produtos()
                    ->withPivot(['id', 'preco', 'preco_promocional', 'estoque', 'destaque', 'ativo'])
                    ->orderBy('loja_produtos.created_at', 'desc');

                if ($builderCallback !== null) {
                    $builderCallback($query);
                }

                $data = $query->paginate($options['per_page'] ?? 15);

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
        if (isset($data['url_imagem']) && $data['url_imagem'] instanceof UploadedFile) {
            $result = $this->uploadService->armazenarFoto(
                $data['url_imagem'],
                null,
                'produtos/'
            );

            $data['url_imagem'] = $this->sanitizeFileName($result, $data['url_imagem']);
        }

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
                $data['produto_id'] = $id;

                if (isset($data['url_imagem']) && $data['url_imagem'] instanceof UploadedFile) {
                    $produto = Produto::find($id);
                    $arquivoAtual = $produto ? basename(parse_url($produto->getRawOriginal('url_imagem'), PHP_URL_PATH)) : null;

                    $result = $this->uploadService->armazenarFoto(
                        $data['url_imagem'],
                        $arquivoAtual,
                        'produtos/'
                    );

                    $data['url_imagem'] = $this->sanitizeFileName($result, $data['url_imagem']);
                }

                return $this->createOrUpdateForStore($data, $loja);
            }
        }

        return parent::update($data, $id);
    }

    /**
     * Saneamento extra para o Heroku
     */
    private function sanitizeFileName(?string $fileName, UploadedFile $file): string
    {
        if (!$fileName) {
            return (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
        }

        // Se o nome do arquivo retornado pelo serviço contiver caminhos ou o prefixo "php" típico de temp do Linux
        if (str_contains($fileName, '/') || str_contains($fileName, '\\') || (str_starts_with($fileName, 'php') && !str_contains($fileName, '.'))) {
            $cleanName = basename($fileName);

            // Se ainda assim parecer um arquivo temporário (sem extensão), forçamos uma extensão baseada no arquivo original
            if (!str_contains($cleanName, '.')) {
                $cleanName .= '.' . ($file->getClientOriginalExtension() ?: 'png');
            }

            return $cleanName;
        }

        return $fileName;
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
            $produtoId = $data['produto_id'] ?? $data['id'] ?? null;

            if ($produtoId) {
                $produto = $this->produto::find($produtoId);
            }

            if (!$produto && !empty($data['ean'])) {
                $produto = $this->produto::where('ean', $data['ean'])->first();
            }

            if (!$produto) {
                if ($loja->tipo_loja !== 'cervejaria') {
                    throw new \Exception('Apenas lojas do tipo Cervejaria podem cadastrar novos produtos.');
                }

                $productData = [
                    'id' => $data['id'] ?? null,
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
                    ];

                    if (array_key_exists('url_imagem', $data)) {
                        $updateData['url_imagem'] = $data['url_imagem'];
                    }

                    $produto->update($updateData);
                }
            }

            // Tratamento de tipos para o MySQL
            $destaque = $data['destaque'] ?? false;
            if (is_string($destaque)) {
                $destaque = filter_var($destaque, FILTER_VALIDATE_BOOLEAN);
            }

            $ativo = $data['ativo'] ?? true;
            if (is_string($ativo)) {
                $ativo = filter_var($ativo, FILTER_VALIDATE_BOOLEAN);
            }

            $pivotData = [
                'id' => (string) \Illuminate\Support\Str::ulid(),
                'preco' => $data['preco'] ?? 0,
                'preco_promocional' => $data['preco_promocional'] ?? null,
                'estoque' => $data['estoque'] ?? 0,
                'destaque' => (bool) $destaque,
                'ativo' => (bool) $ativo,
            ];

            if ($loja->produtos()->where('produto_id', $produto->id)->exists()) {
                $loja->produtos()->updateExistingPivot($produto->id, $pivotData);
            } else {
                $loja->produtos()->attach($produto->id, $pivotData);
            }

            \DB::commit();

            return $produto->load([
                'lojas' => function ($q) use ($loja) {
                    $q->where('lojas.id', $loja->id);
                },
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }
}
