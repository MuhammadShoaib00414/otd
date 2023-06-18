<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HomePageImage extends Model
{
    protected $guarded = ['id'];

    public function getImageUrlAttribute()
    {
        if($url = $this->getRawOriginal('image_url'))
            return getS3Url($url);

        return '';
    }
}
