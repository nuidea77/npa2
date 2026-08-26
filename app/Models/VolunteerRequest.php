<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerRequest extends Model
{
    protected $guarded = [];

    public function org()
    {
        return $this->belongsTo(Org::class);
    }
}
