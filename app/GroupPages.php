<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupPages extends Model
{
    public $fillable = ['user_id', 'content', 'content_template','title', 'slug','displayed_show','show_in_groups','is_active','visibility'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id')->withDefault();
    }
    
}

