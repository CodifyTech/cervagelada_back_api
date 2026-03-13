<?php

namespace App\Domains\Pedido\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Avaliacao\Models\Avaliacao;
use App\Domains\Endereco\Models\Endereco;
use App\Domains\ItemPedido\Models\ItemPedido;
use App\Domains\Loja\Models\Loja;
use App\Domains\Pagamento\Models\Pagamento;
use App\Domains\Pedido\Enums\OrderStatus;
use App\Domains\Shared\Models\BaseModel;
use App\Domains\TransacoesFinanceiras\Models\TransacoesFinanceiras;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pedido extends BaseModel
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $with = ['pagamento'];

    protected $fillable = ['subtotal', 'taxa_entrega', 'total', 'status', 'codigo_rastreamento', 'tempo_estimado_min', 'tempo_estimado_max', 'user_id', 'loja_id', 'entregador_id', 'endereco_id', 'pin_entrega', 'pin_validado_em', 'pin_tentativas'];

    protected $casts = [
        'status' => OrderStatus::class,
        'pin_validado_em' => 'datetime',
        'pin_tentativas' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entregador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entregador_id');
    }

    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }

    public function endereco(): BelongsTo
    {
        return $this->belongsTo(Endereco::class);
    }

    /**
     * Get the itempedidos for this record.
     */
    public function itemPedidos(): HasMany
    {
        return $this->hasMany(ItemPedido::class);
    }

    /**
     * Get the avaliacoes for this record.
     */
    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class);
    }

    /**
     * Get the transacoesfinanceiras for this record.
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
