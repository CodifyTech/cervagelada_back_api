<?php

namespace App\Domains\Loja\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class EntregadorLoja extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'entregador_loja';

    protected $fillable = ['user_id', 'loja_id', 'ativo'];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function entregador(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Auth\Models\User::class, 'user_id');
    }

    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }
}
