<?php

namespace App\Models;

use App\Casts\AsUnicodeJson;
use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $guarded = [];

    protected $casts = ['data' => AsUnicodeJson::class, 'files' => AsUnicodeJson::class];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
