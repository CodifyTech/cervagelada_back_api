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
use App\Domains\Pagamento\Models\Pagamento;

class Pedido extends BaseModel
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $with = ['pagamento'];

    protected $fillable = ['subtotal', 'taxa_entrega', 'total', 'status', 'codigo_rastreamento', 'tempo_estimado_min', 'tempo_estimado_max', 'user_id', 'loja_id', 'endereco_id'];


    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Auth\Models\User::class);
    }

    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }

    public function endereco(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Endereco\Models\Endereco::class);
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

    public function pagamento(): HasOne
    {
        return $this->hasOne(Pagamento::class);
    }
}
