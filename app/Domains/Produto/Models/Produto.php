<?php

namespace App\Domains\Produto\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domains\Shared\Models\BaseModel;


class Produto extends BaseModel
{
    use HasFactory;

    protected $table = 'produtos';

    protected $fillable = [
        'nome', 'descricao', 'marca', 'teor_alcoolico', 'volume_ml',
        'url_imagem', 'pedido_minimo', 'fabricante', 'ean', 'sku', 'atributos',
        'status_aprovacao', 'motivo_reprovacao', 'aprovado_por', 'aprovado_em',
    ];

    protected $casts = [
        'aprovado_em' => 'datetime',
    ];

    public function aprovador(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Auth\Models\User::class, 'aprovado_por');
    }

    public function lojas(): BelongsToMany
    {
        return $this->belongsToMany(\App\Domains\Loja\Models\Loja::class, 'loja_produtos', 'produto_id', 'loja_id')
                    ->using(LojaProduto::class)
                    ->withPivot(['id', 'preco', 'preco_promocional', 'estoque', 'destaque', 'ativo'])
                    ->withTimestamps();
    }

    public function scopeAprovados($query)
    {
        return $query->where('status_aprovacao', 'aprovado');
    }

    public function scopePendentes($query)
    {
        return $query->where('status_aprovacao', 'pendente');
    }
}
