<?php

namespace App\Domains\Endereco\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Shared\Models\BaseModel;
use App\Domains\Auth\Models\User;

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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
