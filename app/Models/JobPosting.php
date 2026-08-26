<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    protected $guarded = [];

    protected $casts = ['open_date' => 'date:Y-m-d', 'close_date' => 'date:Y-m-d'];

    public function org()
    {
        return $this->belongsTo(Org::class);
    }
}
