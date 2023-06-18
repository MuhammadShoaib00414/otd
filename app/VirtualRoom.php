<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VirtualRoom extends Model
{
    protected $guarded = ['id'];
    
    public function clickAreas()
    {
        return $this->hasMany(ClickArea::class);
    }

    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function getImageUrlAttribute()
    {
        if($this->image_path)
            return getS3Url($this->image_path);

        return '';
    }
}
