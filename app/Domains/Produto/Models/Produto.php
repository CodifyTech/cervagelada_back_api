<?php

namespace App\Domains\Produto\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domains\Shared\Models\BaseModel;


class Produto extends BaseModel
{
    use HasFactory;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'produtos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nome', 'descricao', 'marca', 'teor_alcoolico', 'volume_ml', 'url_imagem', 'pedido_minimo', 'fabricante', 'ean', 'sku', 'atributos'];

    /**
     * Get the stores for the product.
     */
    public function lojas(): BelongsToMany
    {
        return $this->belongsToMany(\App\Domains\Loja\Models\Loja::class, 'loja_produtos', 'produto_id', 'loja_id')
                    ->withPivot(['id', 'preco', 'preco_promocional', 'estoque', 'destaque', 'ativo'])
                    ->withTimestamps();
    }
}
