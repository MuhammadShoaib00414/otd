<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    protected $fillable = ['post_id','text','user_id'];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    public function listing()
    {
        return $this->morphOne(Post::class, 'post');
    }

    public function notifications()
    {
        return $this->morphMany(\App\Notification::class, 'notifiable');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'postable_id', 'id')->where('postable_type', 'App\Comments');
    }

    public function hasUserLiked()
    {
        return $this->likes()->where('user_id', auth()->user()->id)->exists();
    }
}

