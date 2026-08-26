<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stamp extends Model
{
    protected $guarded = [];

    protected $casts = ['stamp_date' => 'date:Y-m-d', 'deleted_at' => 'datetime'];

    public function org()
    {
        return $this->belongsTo(Org::class);
    }

    public function logs()
    {
        return $this->hasMany(StampLog::class)->orderByDesc('id');
    }
}
