<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Park extends Model
{
    protected $guarded = [];

    protected $casts = ['featured' => 'boolean'];

    public function org()
    {
        return $this->belongsTo(Org::class);
    }
}
