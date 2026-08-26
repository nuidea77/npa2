<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StampLog extends Model
{
    protected $guarded = [];

    public function stamp()
    {
        return $this->belongsTo(Stamp::class);
    }
}
