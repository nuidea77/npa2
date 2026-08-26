<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * JSON багануудыг юникод escape-гүй (кирилл текстээр нь) хадгална.
 * MariaDB-ийн JSON_CONTAINS \u escape-тэй утгыг тааруулдаггүй тул
 * кириллээр нь хадгалснаар LIKE хайлт бүх DB дээр ажиллана.
 */
class AsUnicodeJson implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value === null ? null : json_decode($value, true);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value === null
            ? null
            : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
