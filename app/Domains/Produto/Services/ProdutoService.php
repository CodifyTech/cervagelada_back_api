<?php

namespace App\Domains\Produto\Services;

use App\Domains\Produto\Models\Produto;
use App\Domains\Shared\Services\BaseService;

class ProdutoService extends BaseService
{
    public function __construct(private readonly Produto $produto)
    {
        $this->setModel($this->produto);
    }

    use \App\Domains\Shared\Traits\S3FileOperations;

    public function createOrUpdateForStore(array $data, \App\Domains\Loja\Models\Loja $loja)
    {
        \DB::beginTransaction();
        try {
            $produto = null;
            $produtoId = $data['produto_id'] ?? null;

            // 1. Find Product
            if ($produtoId) {
                // Find by ID
                $produto = $this->model::find($produtoId);
            } elseif (!empty($data['ean'])) {
                // Find by EAN
                $produto = $this->model::where('ean', $data['ean'])->first();
            }

            // 2. Create Product (if not found)
            if (!$produto) {
                $productData = [
                    'nome' => $data['nome'] ?? null,
                    'descricao' => $data['descricao'] ?? null,
                    'marca' => $data['marca'] ?? null,
                    'teor_alcoolico' => $data['teor_alcoolico'] ?? null,
                    'volume_ml' => $data['volume_ml'] ?? null,
                    // Image upload handled below
                    'pedido_minimo' => $data['pedido_minimo'] ?? null,
                    'fabricante' => $data['fabricante'] ?? null,
                    'ean' => $data['ean'] ?? null,
                    'sku' => $data['sku'] ?? null,
                    'atributos' => $data['atributos'] ?? null,
                ];

                // Remove nulls to avoid overwriting defaults or issues
                //$productData = array_filter($productData, fn($value) => !is_null($value));
                // Actually, create needs all fields. Validation should ensure required ones.

                $produto = $this->model::create($productData);
            }

            // 3. Handle Image Upload (only if it's a new product or upgrading existing?)
            // Usually only the owner or if creating new.
            // Assuming this flow is "Loja creates/edits product".
            // If product is shared, maybe only update image if it doesn't have one?
            // For now, if image is provided, upload it.
            if (isset($data['url_imagem']) && $data['url_imagem'] instanceof \Illuminate\Http\UploadedFile) {
                $fileName = $this->putS3File($data['url_imagem'], 'produtos');
                if ($fileName) {
                    $produto->url_imagem = $fileName;
                    $produto->save();
                }
            }

            // 4. Link/Update Store (Pivot)
            $pivotData = [
                'preco' => $data['preco'],
                'preco_promocional' => $data['preco_promocional'] ?? null,
                'estoque' => $data['estoque'],
                'destaque' => $data['destaque'] ?? false,
                'ativo' => $data['ativo'] ?? true,
            ];

            if ($loja->produtos()->where('produto_id', $produto->id)->exists()) {
                $loja->produtos()->updateExistingPivot($produto->id, $pivotData);
            } else {
                $loja->produtos()->attach($produto->id, $pivotData);
            }

            \DB::commit();
            return $produto;

        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

}
