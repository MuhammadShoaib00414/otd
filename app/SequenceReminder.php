<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sequence;

class SequenceReminder extends Model
{
    protected $guarded = ['id'];

    public function sequence()
    {
        return $this->belongsTo(Sequence::class);
    }
}
