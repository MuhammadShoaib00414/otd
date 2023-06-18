<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    protected $guarded = ['id'];

    public function groups() {
        return $this->belongsToMany(Group::class);
    }
}
