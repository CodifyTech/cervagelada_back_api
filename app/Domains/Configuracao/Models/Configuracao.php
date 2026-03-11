<?php

namespace App\Domains\Configuracao\Models;

use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Configuracao extends BaseModel
{
    use HasFactory;

    protected $table = 'configuracoes';

    protected $fillable = ['chave', 'valor', 'tipo', 'grupo'];

    public function getValorAttribute($value): mixed
    {
        return match ($this->tipo) {
            'json'    => json_decode($value, true),
            'boolean' => (bool) $value,
            default   => $value,
        };
    }

    public function setValorAttribute(mixed $value): void
    {
        $this->attributes['valor'] = is_array($value) ? json_encode($value) : $value;
    }
}
