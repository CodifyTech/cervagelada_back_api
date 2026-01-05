<?php

namespace App\Domains\Endereco\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
    protected $fillable = ['apelido', 'cep', 'rua', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'latitude', 'longitude', 'principal', 'user_id'];
    

    /**
     * Get the useres for this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function useres(): HasMany
    {
        return $this->hasMany(User::class);
    }
}