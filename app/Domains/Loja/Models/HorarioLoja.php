<?php

namespace App\Domains\Loja\Models;

use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorarioLoja extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'horario_lojas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['loja_id', 'abertura', 'fechamento', 'dia_semana'];

    /**
     * Get the Loja that owns this HorarioLoja.
     */
    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }
}
