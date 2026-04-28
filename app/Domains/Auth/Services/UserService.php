<?php

namespace App\Domains\Auth\Services;

use App\Domains\ACL\Models\Role;
use App\Domains\Auth\Models\User;
use App\Domains\Shared\Services\BaseService;
use App\Domains\Shared\Services\UploadService;
use App\Domains\Shared\Utils\IntHelper;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;

class UserService extends BaseService
{
    public function __construct(
        private readonly User $user,
        private readonly Role $role,
        private readonly UploadService $uploadService
    ) {
        $this->setModel($this->user);
    }

    public function index(array $options = [], ?\Closure $builderCallback = null)
    {
        return parent::index($options, function ($query) use ($options) {
            $query->whereHas('belongsToManyRoles', function (Builder $query) {
                $query->whereNot('slug', 'admin');
            })
                ->when(isset($options['sort_by']), function (Builder $query) use ($options) {
                    $query->orderBy($options['sort_by'], $options['sort_order'] ?? 'asc');
                });
        });
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['foto']) && $data['foto'] instanceof UploadedFile) {
                $data['foto'] = $this->uploadService->armazenarFoto(
                    $data['foto'],
                    null,
                    'users/'
                );
            }

            $user = $this->user->create($data);

            if (config('app.email_verification', false)) {
                $user->sendEmailVerificationNotification();
            } else {
                $user->markEmailAsVerified();
            }

            $user->assignRole($data['role']);

            return $user;
        });
    }

    public function update(array $data, string $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $user = $this->findById($id);

            if (isset($data['foto']) && $data['foto'] instanceof UploadedFile) {
                $arquivoAtual = basename(parse_url($user->getRawOriginal('foto'), PHP_URL_PATH));
                $data['foto'] = $this->uploadService->armazenarFoto(
                    $data['foto'],
                    $arquivoAtual,
                    'users/'
                );
            }

            $user->update($data);
            $user->syncRoles($data['role']);

            return $user;
        });
    }

    public function roles($options)
    {
        $data = $this->role
            ->select([
                'name',
                'slug',
            ])
            ->paginate(IntHelper::tryParser($options['per_page'] ?? 15) ?? 15);

        return [
            'data' => $data->items(),
            'total' => $data->total(),
            'page' => $data->currentPage(),
        ];
    }
}
