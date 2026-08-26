<?php

namespace App\Models;

use App\Casts\AsUnicodeJson;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = [];

    protected $casts = [
        'reg_start' => 'date:Y-m-d',
        'reg_end' => 'date:Y-m-d',
        'login_required' => 'boolean',
        'questions' => AsUnicodeJson::class,
    ];

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Бүртгэлийн бодит төлөв:
     * soon (тун удахгүй) | not_started (эхлээгүй) | open (нээлттэй) | closed (дууссан)
     */
    public function regState(): string
    {
        if ($this->status === 'soon') return 'soon';
        if ($this->status === 'open') return 'open';
        if ($this->status === 'closed') return 'closed';
        // auto: огноогоор тооцно
        if (!$this->reg_start || !$this->reg_end) return 'soon';
        $today = now()->startOfDay();
        if ($today->lt($this->reg_start)) return 'not_started';
        if ($today->gt($this->reg_end)) return 'closed';
        return 'open';
    }
}
