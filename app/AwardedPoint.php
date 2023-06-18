<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AwardedPoint extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function point()
    {
    	return $this->belongsTo(Point::class);
    }
}
