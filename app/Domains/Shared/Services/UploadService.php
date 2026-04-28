<?php

namespace App\Domains\Shared\Services;

use App\Exceptions\FailedToUploadArchiveException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadService
{
    public function armazenarFoto(
        object $fotoRequest,
        ?string $arquivoAtual,
        string $caminhoS3 = 'fotos_usuario/'
    ): string {
        $amazonS3 = Storage::disk('s3');
        if ($amazonS3->get($caminhoS3 . $arquivoAtual)) {
            $amazonS3->delete($caminhoS3 . $arquivoAtual);
        }
        $extensaoArquivo = $fotoRequest->getClientOriginalExtension();
        $nome = Str::ulid() . '.' . $extensaoArquivo;
        try {
            $fotoRequest->storePubliclyAs($caminhoS3, $nome, 's3');
        } catch (\Exception $e) {
            throw new \Exception(
                'Falha ao enviar o arquivo, por favor tente novamente mais tarde',
            );
        }

        return $nome;
    }

    /**
     * Exclui o arquivo no Bucket S3
     * @param mixed $arquivo
     * @param mixed $caminho
     * @return void
     */
    public function apagarArquivo($arquivo, $caminho): void
    {
        $amazon = Storage::disk('s3');
        $nome = $caminho . $arquivo;
        if ($amazon->get($nome)) {
            $amazon->delete($nome);
        }
    }
}
