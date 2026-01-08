<?php

namespace App\Domains\Promocao\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domains\Shared\Models\BaseModel;
use App\Domains\Loja\Models\Loja;

class Promocao extends BaseModel
{
    use HasFactory;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'promocoes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['titulo', 'descricao', 'data_inicio', 'data_fim', 'ativo', 'loja_id'];


    /**
     * Get the Loja that owns this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }

    /**
     * Get the products for the promotion.
     */
    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(\App\Domains\Produto\Models\Produto::class, 'produto_promocoes', 'promocao_id', 'produto_id')
                    ->withPivot(['id', 'preco_promocional'])
                    ->withTimestamps();
    }
}
