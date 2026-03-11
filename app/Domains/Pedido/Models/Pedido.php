<?php

namespace App\Domains\Pedido\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domains\Shared\Models\BaseModel;
use App\Domains\Loja\Models\Loja;
use App\Domains\ItemPedido\Models\ItemPedido;
use App\Domains\Avaliacao\Models\Avaliacao;
use App\Domains\TransacoesFinanceiras\Models\TransacoesFinanceiras;

class Pedido extends BaseModel
{
    use HasFactory;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pedidos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['subtotal', 'taxa_entrega', 'total', 'status', 'codigo_rastreamento', 'tempo_estimado_min', 'tempo_estimado_max', 'user_id', 'loja_id'];


    /**
     * Get the User that owns this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Auth\Models\User::class);
    }

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
     * Get the itempedidos for this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function itemPedidos(): HasMany
    {
        return $this->hasMany(ItemPedido::class);
    }
    /**
     * Get the avaliacoes for this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class);
    }

    /**
     * Get the transacoesfinanceiras for this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transacoesFinanceiras(): HasMany
    {
        return $this->hasMany(TransacoesFinanceiras::class);
    }
}