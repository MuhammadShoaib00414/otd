<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegisterInvitation extends Model
{

    protected $fillable = [
        'register_page_id',
        'unique_id',
        'expired_id',
        'user_id',
        'register_date_time',
    ];
}
