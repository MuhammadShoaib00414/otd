<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Introduction extends Model
{
    use SoftDeletes;

    protected $table = 'introductions';

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function invitee()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'sent_by')->withTrashed();
    }

    public function getOtherUserAttribute()
    {
       	return $this->users->filter(function ($value, $key) {
            return $value->id != \Request::user()->id;
        })->first();
    }

    public function otherUser($userId)
    {
        return $this->users->where('id', '!=', $userId)->first();
    }

    public function hasUserSentMessage($userId)
    {
        if($this->are_messages_sent && array_key_exists($userId, json_decode($this->are_messages_sent, true)))
        {
            return json_decode($this->are_messages_sent, true)[$userId];
        }
        else
            return false;
    }

    public function scopeParticipants($query, $userIds)
    {
        return $query->join('introduction_user', 'introductions.id', '=', 'introduction_user.introduction_id')->whereIn('user_id', $userIds);
    }

    public function notifications()
    {
        return $this->morphMany(\App\Notification::class, 'notifiable');
    }

    public function notificationsFor($userId)
    {
        return $this->morphMany(\App\Notification::class, 'notifiable')->where('user_id', $userId);
    }
}
