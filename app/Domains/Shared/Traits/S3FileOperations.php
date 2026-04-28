<?php

namespace App\Domains\Shared\Traits;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

trait S3FileOperations
{
    public function getS3FileUrl($fileName, $path, $timestamp = null): ?string
    {
        if (! isset($fileName)) {
            return null;
        }

        $file = "$path/$fileName";

        return Storage::disk('s3')->url($file);
        // .'?t='.now()->addMinutes(5)->timestamp;
    }

    public function putS3File($file, string $path): ?string
    {
        try {
            if ($file instanceof UploadedFile) {
                $fileName = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
                Storage::disk('s3')->putFileAs($path, $file, $fileName, [
                    'visibility' => 'public',
                ]);

                return $fileName;
            }

            if (is_string($file) && preg_match('/^data:image\/(\w+);base64,/', $file, $type)) {
                $data = substr($file, strpos($file, ',') + 1);
                $data = base64_decode($data);

                if ($data === false) {
                    return null;
                }

                $extension = strtolower($type[1]);
                if ($extension === 'jpeg') {
                    $extension = 'jpg';
                }

                $fileName = Str::uuid()->toString().'.'.$extension;
                Storage::disk('s3')->put("$path/$fileName", $data, [
                    'visibility' => 'public',
                ]);

                return $fileName;
            }

            return null;
        } catch (Exception $e) {
            \Log::error('Erro no upload S3 (putS3File): '.$e->getMessage());

            return null;
        }
    }

    public function putS3FileIfNotExists($file, string $path, $fileName = null): ?string
    {
        if (empty($fileName) || empty($file)) {
            return null;
        }

        // Se for uma string base64, usamos o putS3File direto
        if (is_string($file) && preg_match('/^data:image\/(\w+);base64,/', $file)) {
            return $this->putS3File($file, $path);
        }

        if (is_string($file)) {
            // Se já for um nome de arquivo (não um caminho local), retorna apenas ele
            if (! @is_file($file)) {
                return basename($file);
            }
        }

        try {
            // Verificar se o arquivo é uma imagem
            $mimeType = null;
            if ($file instanceof UploadedFile) {
                $mimeType = $file->getMimeType();
            } elseif (is_string($file) && ! empty($file) && @is_file($file)) {
                $mimeType = @mime_content_type($file);
            }

            if ($mimeType && str_starts_with($mimeType, 'image/')) {
                // Otimização: Redimensionar imagens grandes antes do upload
                $manager = new ImageManager(new Driver);
                $imagePath = ($file instanceof UploadedFile) ? $file->getRealPath() : $file;
                $image = $manager->read($imagePath);

                // Redimensionar imagens maiores que 2000px
                $width = $image->width();
                if ($width > 2000) {
                    $image = $image->resize(2000, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                // Nome do arquivo
                $fileHash = $fileName.'.webp';
                $name = "$path/$fileHash";

                // Comprimir e upload
                $imageData = $image->toWebp(85);
                Storage::disk('s3')->put($name, $imageData, [
                    'visibility' => 'public',
                ]);

                return $fileHash;
            } else {
                // Processar arquivos que não são imagens ou se mimeType falhou
                $extension = ($file instanceof UploadedFile) ? $file->getClientOriginalExtension() : pathinfo($file, PATHINFO_EXTENSION);
                $fileHash = $fileName.'.'.$extension;
                $name = "$path/$fileHash";

                if ($file instanceof UploadedFile) {
                    Storage::disk('s3')->putFileAs($path, $file, basename($name), [
                        'visibility' => 'public',
                    ]);
                } else {
                    Storage::disk('s3')->put($name, @file_get_contents($file), [
                        'visibility' => 'public',
                    ]);
                }

                return $fileHash;
            }
        } catch (Exception $e) {
            \Log::error('Erro no upload S3 (putS3FileIfNotExists): '.$e->getMessage());

            return null;
        }
    }

    public function deleteS3File(string $path): bool
    {
        try {
            if (Storage::disk('s3')->exists($path)) {
                return Storage::disk('s3')->delete($path);
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
