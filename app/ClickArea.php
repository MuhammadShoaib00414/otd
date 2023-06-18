<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClickArea extends Model
{
    protected $guarded = ['id'];
    
    public function virtualRoom()
    {
        return $this->belongsTo(VirtualRoom::class);
    }
}
