<?php

namespace App\Models;

use App\Casts\AsUnicodeJson;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $guarded = [];

    protected $casts = [
        'published_at' => 'date:Y-m-d',
        'positions' => AsUnicodeJson::class,
        'regions' => AsUnicodeJson::class,
    ];
}
