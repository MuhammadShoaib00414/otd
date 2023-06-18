<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
    protected $guarded = ['id'];

    public function group()
    {
        return $this->hasOne(Group::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function completedBadge()
    {
        return $this->belongsTo(Option::class, 'completed_badge_id');
    }

    public function reminders()
    {
        return $this->hasMany(SequenceReminder::class);
    }
}
