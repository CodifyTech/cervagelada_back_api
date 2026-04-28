<?php

namespace App\Domains\Loja\Models;

use App\Casts\S3FileUrlCast;
use App\Domains\Auth\Models\User;
use App\Domains\Avaliacao\Models\Avaliacao;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Produto\Models\LojaProduto;
use App\Domains\Produto\Models\Produto;
use App\Domains\Promocao\Models\Promocao;
use App\Domains\Shared\Models\BaseModel;
use App\Domains\TransacoesFinanceiras\Models\TransacoesFinanceiras;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loja extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lojas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nome_fantasia', 'url_logo', 'tipo_loja', 'latitude', 'longitude', 'raio_entrega_km', 'tempo_entrega_min', 'tempo_entrega_max', 'aceite_automatico', 'pedido_minimo', 'taxa_comissao', 'ativo', 'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado'];

    protected $casts = [
        'url_logo' => S3FileUrlCast::class,
    ];

    public string $fileDir = 'lojas';

    /**
     * Scope to filter stores within delivery radius using Haversine formula.
     */
    public function scopePorRaio(Builder $query, float $lat, float $lng): Builder
    {
        $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))';

        if (is_null($query->getQuery()->columns)) {
            $query->select($this->getTable().'.*');
        }

        return $query
            ->addSelect(\DB::raw("{$haversine} AS distancia"))
            ->addBinding([$lat, $lng, $lat], 'select')
            ->whereRaw("{$haversine} <= raio_entrega_km", [$lat, $lng, $lat])
            ->orderByRaw("{$haversine} ASC", [$lat, $lng, $lat]);
    }

    /**
     * Get the products for the store.
     */
    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'loja_produtos', 'loja_id', 'produto_id')
            ->using(LojaProduto::class)
            ->withPivot(['id', 'preco', 'preco_promocional', 'estoque', 'destaque', 'ativo'])
            ->withTimestamps();
    }

    /**
     * Get the horarios for the store.
     */
    public function horarios(): HasMany
    {
        return $this->hasMany(HorarioLoja::class);
    }

    /**
     * Get the promocoes for this record.
     */
    public function promocoes(): HasMany
    {
        return $this->hasMany(Promocao::class);
    }

    /**
     * Get the pedidos for this record.
     */
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    /**
     * Get the avaliacoes for this record.
     */
    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class);
    }

    /**
     * Get the transacoesfinanceiras for this record.
     */
    public function transacoesFinanceiras(): HasMany
    {
        return $this->hasMany(TransacoesFinanceiras::class);
    }

    /**
     * Entregadores associados a esta loja (multi-loja).
     */
    public function entregadores(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'entregador_loja', 'loja_id', 'user_id')
            ->withPivot(['id', 'ativo'])
            ->withTimestamps();
    }
}
