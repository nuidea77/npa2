<?php

namespace App\Models;

use App\Casts\AsUnicodeJson;
use Illuminate\Database\Eloquent\Model;

class Org extends Model
{
    protected $guarded = [];

    protected $casts = [
        'aimags' => AsUnicodeJson::class,
        'volunteer_durations' => AsUnicodeJson::class,
        'accepts_volunteers' => 'boolean',
    ];

    public function parks()
    {
        return $this->hasMany(Park::class);
    }

    public function stamps()
    {
        return $this->hasMany(Stamp::class);
    }
}
