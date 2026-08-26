<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StampYear extends Model
{
    protected $guarded = [];

    protected $casts = ['start_date' => 'date:Y-m-d', 'end_date' => 'date:Y-m-d'];
}
