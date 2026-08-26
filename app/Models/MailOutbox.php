<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailOutbox extends Model
{
    protected $table = 'mail_outbox';

    protected $guarded = [];
}
