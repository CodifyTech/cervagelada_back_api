<?php

namespace App\Domains\Noticias\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domains\Shared\Models\BaseModel;


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
    protected $fillable = ['titulo', 'conteudo', 'url_imagem', 'publicado_em', 'ativo'];
    
}
