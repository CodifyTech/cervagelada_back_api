<?php

namespace App\Domains\Noticias\Services;

use App\Domains\Noticias\Models\Noticias;
use App\Domains\Shared\Services\BaseService;
use App\Domains\Shared\Services\UploadService;
use Illuminate\Http\UploadedFile;

class NoticiasService extends BaseService
{
    public function __construct(
        private readonly Noticias $noticias,
        private readonly UploadService $uploadService
    ) {
        $this->setModel($this->noticias);
    }

    public function store(array $data)
    {
        if (isset($data['url_imagem']) && $data['url_imagem'] instanceof UploadedFile) {
            $data['url_imagem'] = $this->uploadService->armazenarFoto(
                $data['url_imagem'],
                null,
                'noticias/'
            );
        }

        return parent::store($data);
    }

    public function update(array $data, string $id)
    {
        $noticia = $this->findById($id);

        if (isset($data['url_imagem']) && $data['url_imagem'] instanceof UploadedFile) {
            $arquivoAtual = basename(parse_url($noticia->getRawOriginal('url_imagem'), PHP_URL_PATH));
            $data['url_imagem'] = $this->uploadService->armazenarFoto(
                $data['url_imagem'],
                $arquivoAtual,
                'noticias/'
            );
        }

        return parent::update($data, $id);
    }
}
