<?php

namespace App\Domains\Endereco\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Endereco extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'enderecos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['apelido', 'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'latitude', 'longitude', 'principal', 'user_id'];

    /**
     * Get the user that owns this address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
