<?php

namespace App\Domains\Destaque\Services;

use App\Domains\Destaque\Models\Destaque;
use App\Domains\Shared\Services\BaseService;

use App\Domains\Shared\Helpers\SortHelper;
use App\Domains\Shared\Interfaces\IService;
use App\Domains\Shared\Utils\IntHelper;
use Illuminate\Database\Eloquent\Builder;

class DestaqueService extends BaseService
{
    public function __construct(private readonly Destaque $destaque)
    {
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
}
