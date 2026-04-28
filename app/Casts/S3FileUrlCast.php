<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class S3FileUrlCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Se já for uma URL completa, retorná-lo diretamente
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Limpeza de segurança: Se o valor contiver caminhos locais (C:\ ou /tmp/), pega só o final
        if (str_contains($value, '\\') || str_contains($value, '/')) {
            $parts = preg_split('/[\/\\\]/', $value);
            $value = end($parts);
        }

        // Determinar o path baseado na tabela
        $path = $model->getTable().'/';

        return Storage::disk('s3')->url($path.$value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if (empty($value)) {
            return null;
        }

        // Se for uma URL completa vindo do frontend, extraímos apenas o filename
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return basename(parse_url($value, PHP_URL_PATH));
        }

        // Se for um caminho local que escapou do Service, limpamos aqui também
        if (str_contains($value, '\\') || str_contains($value, '/')) {
            $parts = preg_split('/[\/\\\]/', $value);

            return end($parts);
        }

        return $value;
    }
}
