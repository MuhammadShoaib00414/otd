<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shoutout extends Model
{
    protected $guarded = ['id'];

    public function shouting()
    {
        return $this->belongsTo(User::class, 'shoutout_by')->withTrashed();
    }

    public function shouted()
    {
        return $this->belongsTo(User::class, 'shoutout_to')->withTrashed();
    }

    public function listing()
    {
        return $this->morphOne(Post::class, 'post');
    }

    public function notifications()
    {
        return $this->morphMany('App\Notification', 'notifiable');
    }

    public function notificationsFor($userId)
    {
        return $this->morphMany('App\Notification', 'notifiable')->where('user_id', $userId);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'shoutout_by')->withTrashed();
    }
}
