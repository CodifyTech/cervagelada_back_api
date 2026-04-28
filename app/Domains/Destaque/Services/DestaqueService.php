<?php

namespace App\Domains\Destaque\Services;

use App\Domains\Destaque\Models\Destaque;
use App\Domains\Shared\Services\BaseService;
use App\Domains\Shared\Services\UploadService;
use App\Domains\Shared\Helpers\SortHelper;
use App\Domains\Shared\Utils\IntHelper;
use Illuminate\Http\UploadedFile;

class DestaqueService extends BaseService
{
    public function __construct(
        private readonly Destaque $destaque,
        private readonly UploadService $uploadService
    ) {
        $this->setModel($this->destaque);
    }

    public function index(array $options = [], ?\Closure $builderCallback = null)
    {
        $query = $this->destaque->newQuery();

        if ($builderCallback !== null) {
            $builderCallback($query);
        }

        $sortBy = $options['sort_by'] ?? 'id';
        $sortOrder = $options['sort_order'] ?? 'desc';

        $query = SortHelper::applySort($query, $sortBy, $sortOrder);

        $query->with('produto:nome,id');

        $data = $query->paginate(IntHelper::tryParser($options['per_page'] ?? 15) ?? 15);

        return [
            'data' => $data->items(),
            'total' => $data->total(),
            'page' => $data->currentPage(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
        ];
    }

    public function store(array $data)
    {
        if (isset($data['imagem']) && $data['imagem'] instanceof UploadedFile) {
            $data['imagem'] = $this->uploadService->armazenarFoto(
                $data['imagem'],
                null,
                'destaques/'
            );
        }

        return parent::store($data);
    }

    public function update(array $data, string $id)
    {
        $destaque = $this->findById($id);

        if (isset($data['imagem']) && $data['imagem'] instanceof UploadedFile) {
            $arquivoAtual = basename(parse_url($destaque->getRawOriginal('imagem'), PHP_URL_PATH));
            $data['imagem'] = $this->uploadService->armazenarFoto(
                $data['imagem'],
                $arquivoAtual,
                'destaques/'
            );
        }

        return parent::update($data, $id);
    }
}
