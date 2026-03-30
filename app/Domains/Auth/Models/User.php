<?php

namespace App\Domains\Auth\Models;

use App\Casts\UploadCast;
use App\Domains\ACL\Models\Role;
use App\Domains\ACL\Traits\HasRoles;
use App\Domains\Auth\Notifications\ResetPasswordNotification;
use App\Domains\Auth\Notifications\VerifyEmail;
use App\Domains\Avaliacao\Models\Avaliacao;
use App\Domains\Endereco\Models\Endereco;
use App\Domains\Loja\Models\Loja;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Shared\Traits\TenantScope;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $foto
 * @property int $termos
 * @property int $ativo
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Role> $belongsToManyRoles
 * @property-read int|null $belongs_to_many_roles_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read mixed $role
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAtivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTermos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 *
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, HasRoles, HasUlids, MustVerifyEmail, Notifiable, TenantScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'telefone',
        'cpf',
        'password',
        'loja_id',
        'foto',
        'termos',
        'ativo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'roles',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'foto' => UploadCast::class,
    ];

    protected $appends = [
        'role',
    ];

    protected array $list = [
        'id',
        'name',
        'email',
        'telefone',
        'foto',
    ];

    protected $primaryKey = 'id';

    protected $table = 'users';

    public string $fileDir = 'users';

    // region Attributes
    public function role(): Attribute
    {
        return Attribute::make(
            get: function () {
                $role = $this->getFirstRole();
                if ($role) {
                    return [
                        'name' => $role['name'] ?? 'N/A',
                        'slug' => $role['slug'] ?? 'N/A',
                    ];
                }

                return null;
            },
        );
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    // endregion
    // region Relations
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // endregion
    // region Methods
    public function sendPasswordResetNotification($token): void
    {
        $url = env('FRONT_END_URL').'/admin/auth/redefinir-senha?email='.$this->email.'&token='.$token;
        $this->notify(new ResetPasswordNotification($url));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }
    // endregion

    /**
     * Get the Endereco that owns this record.
     */
    public function endereco(): BelongsTo
    {
        return $this->belongsTo(Endereco::class);
    }

    /**
     * Get the Loja that owns this record.
     */
    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }

    /**
     * Lojas onde o entregador atua (multi-loja).
     */
    public function lojasEntregador(): BelongsToMany
    {
        return $this->belongsToMany(Loja::class, 'entregador_loja', 'user_id', 'loja_id')
            ->withPivot(['id', 'ativo'])
            ->withTimestamps();
    }

    /**
     * Pedidos onde o user é o entregador.
     */
    public function pedidosEntrega(): HasMany
    {
        return $this->hasMany(Pedido::class, 'entregador_id');
    }

    /**
     * Get the avaliacoes for this record.
     */
    public function avaliacoes(): HasMany
    {
        return $this->hasMany(Avaliacao::class);
    }
}
