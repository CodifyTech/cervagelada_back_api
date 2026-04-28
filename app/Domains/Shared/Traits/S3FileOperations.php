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
        if (! isset($fileName) || empty($fileName)) {
            return null;
        }

        // Se já for uma URL completa (ex: vinda de um erro anterior), tenta extrair o nome
        if (filter_var($fileName, FILTER_VALIDATE_URL)) {
            $fileName = basename(parse_url($fileName, PHP_URL_PATH));
        }

        $file = "$path/$fileName";

        return Storage::disk('s3')->url($file);
    }

    public function putS3File($file, string $path, $customFileName = null): ?string
    {
        try {
            $extension = ($file instanceof UploadedFile) ? $file->getClientOriginalExtension() : pathinfo($file, PATHINFO_EXTENSION);
            if (empty($extension)) {
                $extension = 'png';
            }

            $fileName = ($customFileName ?: Str::uuid()->toString()).'.'.$extension;

            // Se for objeto UploadedFile, pegamos o path real do temp do PHP
            $imagePath = ($file instanceof UploadedFile) ? $file->getRealPath() : $file;

            if (empty($imagePath) || ! is_file($imagePath)) {
                \Log::error('S3 Upload: Arquivo inválido ou vazio em '.$imagePath);

                return null;
            }

            $content = file_get_contents($imagePath);

            \Log::info("S3 Upload: Enviando $fileName para $path");

            Storage::disk('s3')->put("$path/$fileName", $content, [
                'visibility' => 'public',
            ]);

            return $fileName;
        } catch (Exception $e) {
            \Log::error('Erro no upload S3 (putS3File): '.$e->getMessage());

            return null;
        }
    }

    public function putS3FileIfNotExists($file, string $path, $fileName = null): ?string
    {
        if (is_null($fileName) || is_null($file)) {
            return null;
        }

        try {
            $mimeType = ($file instanceof UploadedFile) ? $file->getMimeType() : @mime_content_type($file);

            if ($mimeType && str_starts_with($mimeType, 'image/')) {
                $manager = new ImageManager(new Driver);
                $imagePath = ($file instanceof UploadedFile) ? $file->getRealPath() : $file;
                $image = $manager->read($imagePath);

                $width = $image->width();
                if ($width > 2000) {
                    $image = $image->resize(2000, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                $fileHash = $fileName.'.webp';
                $name = "$path/$fileHash";

                $imageData = $image->toWebp(85);
                Storage::disk('s3')->put($name, $imageData, [
                    'visibility' => 'public',
                ]);

                return $fileHash;
            } else {
                $extension = ($file instanceof UploadedFile) ? $file->getClientOriginalExtension() : pathinfo($file, PATHINFO_EXTENSION);
                $fileHash = $fileName.'.'.$extension;
                $name = "$path/$fileHash";

                if (is_string($file)) {
                    Storage::disk('s3')->put($name, file_get_contents($file), [
                        'visibility' => 'public',
                    ]);
                } else {
                    Storage::disk('s3')->putFileAs($path, $file, basename($name), [
                        'visibility' => 'public',
                    ]);
                }

                return $fileHash;
            }
        } catch (Exception $e) {
            \Log::error('Erro no upload S3: '.$e->getMessage());

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
