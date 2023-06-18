<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VideoRoom extends Model
{
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::creating(function ($videoRoom) {
            $videoRoom->slug = Str::uuid();
        });
    }
}
