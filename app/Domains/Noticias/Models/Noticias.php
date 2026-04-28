<?php

namespace App\Domains\Noticias\Models;

use App\Casts\S3FileUrlCast;
use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Noticias extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'noticias';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titulo', 'conteudo', 'url_imagem', 'publicado_em', 'ativo',
        'fonte', 'url_fonte', 'patrocinado', 'patrocinador',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'patrocinado' => 'boolean',
        'publicado_em' => 'datetime',
        'url_imagem' => S3FileUrlCast::class,
    ];
}
