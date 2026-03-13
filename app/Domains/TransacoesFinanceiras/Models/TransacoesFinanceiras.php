<?php

namespace App\Domains\TransacoesFinanceiras\Models;

use App\Domains\Loja\Models\Loja;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransacoesFinanceiras extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transacoes_financeiras';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['tipo', 'valor', 'descricao', 'liquidado', 'liquidado_em', 'loja_id', 'pedido_id'];

    /**
     * Get the Loja that owns this record.
     */
    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }

    /**
     * Get the Pedido that owns this record.
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
