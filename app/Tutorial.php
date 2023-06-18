<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    protected $guarded = ['id'];

    protected $dates = ['created_at', 'updated_at'];

    public function scopeNamed($query, $name)
    {
    	return $query->where('name', $name)->first();
    }
}
