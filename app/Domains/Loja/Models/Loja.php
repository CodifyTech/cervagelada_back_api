<?php

namespace App\Domains\Loja\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domains\Shared\Models\BaseModel;
use App\Domains\Promocao\Models\Promocao;

class Loja extends BaseModel
{
    use HasFactory;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lojas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nome_fantasia', 'tipo_loja', 'latitude', 'longitude', 'raio_entrega_km', 'tempo_entrega_min', 'tempo_entrega_max', 'aceite_automatico', 'pedido_minimo', 'taxa_comissao', 'ativo', 'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado'];

    /**
     * Get the products for the store.
     */
    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(\App\Domains\Produto\Models\Produto::class, 'loja_produtos', 'loja_id', 'produto_id')
            ->withPivot(['id', 'preco', 'preco_promocional', 'estoque', 'destaque', 'ativo'])
            ->withTimestamps();
    }

    /**
     * Get the horarios for the store.
     */
    public function horarios(): HasMany
    {
        return $this->hasMany(HorarioLoja::class);
    }

    /**
     * Get the promocoes for this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function promocoes(): HasMany
    {
        return $this->hasMany(Promocao::class);
    }
}