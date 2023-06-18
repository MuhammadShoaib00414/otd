<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageParticipant extends Model
{
	use SoftDeletes;

    protected $dates = ['left_at', 'deleted_at'];
}
