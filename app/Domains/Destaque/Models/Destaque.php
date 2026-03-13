<?php

namespace App\Domains\Destaque\Models;

use App\Casts\UploadCast;
use App\Domains\Produto\Models\Produto;
use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Destaque extends BaseModel
{
    use HasFactory;

    protected $table = 'destaques';

    protected $fillable = [
        'titulo', 'descricao', 'imagem', 'video_url',
        'cta_texto', 'cta_url', 'produto_id',
        'valor_contrato', 'data_inicio', 'data_fim', 'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'valor_contrato' => 'float',
        'imagem' => UploadCast::class,
    ];

    public function scopeAtivo(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    public function scopeVigente(Builder $query): Builder
    {
        return $query->where('data_inicio', '<=', now())
            ->where('data_fim', '>=', now());
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}
