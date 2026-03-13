<?php

namespace App\Domains\ItemPedido\Models;

use App\Domains\Pedido\Models\Pedido;
use App\Domains\Produto\Models\Produto;
use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPedido extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'item_pedidos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['quantidade_solicitada', 'quantidade_final', 'preco_unitario', 'preco_total', 'ajuste_preco', 'observacoes', 'pedido_id', 'produto_id'];

    /**
     * Get the Pedido that owns this record.
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Get the Produto that owns this record.
     */
    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}
