<?php

namespace App;

use App\Post;
use Illuminate\Database\Eloquent\Model;

class ScheduledPost extends Model
{
    protected $guarded = ['id'];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function post()
    {
    	return $this->belongsTo(Post::class);
    }
}
