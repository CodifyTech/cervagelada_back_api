<?php

namespace App\Domains\Avaliacao\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domains\Shared\Models\BaseModel;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Auth\Models\User;
use App\Domains\Loja\Models\Loja;class Avaliacao extends BaseModel
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
    /**
     * Get the User that owns this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}