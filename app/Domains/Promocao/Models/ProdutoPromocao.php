<?php

namespace App\Domains\Promocao\Models;

use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProdutoPromocao extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'produto_promocoes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['promocao_id', 'produto_id', 'preco_promocional'];
}
