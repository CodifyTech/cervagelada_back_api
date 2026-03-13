<?php

namespace App\Domains\Pagamento\Models;

use App\Domains\Pedido\Models\Pedido;
use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends BaseModel
{
    use HasFactory;

    protected $table = 'pagamentos';

    protected $fillable = [
        'pedido_id',
        'loja_id',
        'asaas_charge_id',
        'asaas_customer_id',
        'metodo',
        'status',
        'valor',
        'pago_em',
        'webhook_payload',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'pago_em' => 'datetime',
        'webhook_payload' => 'array',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
