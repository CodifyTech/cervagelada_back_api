<?php

namespace App\Domains\Avaliacao\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Loja\Models\Loja;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avaliacao extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'avaliacoes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['avaliacao', 'comentario', 'pedido_id', 'user_id', 'loja_id'];

    /**
     * Get the Pedido that owns this record.
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Get the User that owns this record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the Loja that owns this record.
     */
    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }
}
