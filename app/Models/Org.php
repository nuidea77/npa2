<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Org extends Model
{
    protected $guarded = [];

    protected $casts = [
        'aimags' => 'array',
        'volunteer_durations' => 'array',
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
