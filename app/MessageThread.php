<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class MessageThread extends Model
{
    use Cachable;
    
    protected $fillable = ['event_id', 'type'];

    public function participants()
    {
        return $this->belongsToMany(\App\User::class, 'message_participants')->withPivot('left_at');
    }

    public function messages()
    {
        return $this->hasMany(\App\Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(\App\Message::class)->latest();
    }

    public function getOtherUsersAttribute()
    {
        return $this->participants()->where('users.id', '!=', \Auth::user()->id)->get();
    }

    public function getFirstMessageAttribute()
    {
        return $this->messages()->first();
    }

    public function getLastMessageAttribute()
    {
        return $this->messages()->orderBy('created_at', 'desc')->first();
    }

    public function getIsUnreadAttribute()
    {
        return $this->messages->whereNull('recipient_read_at')->where('sending_user_id', '!=', \Auth::user()->id)->count();
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function getReadableType()
    {
        $types = [
            'waitlisted' => 'waitlist',
            'attending' => 'attendees',
            'interested' => 'interested in attending',
        ];

        return $types[$this->type];
    }

    public function notifications()
    {
        return $this->morphMany('App\Notification', 'notifiable');
    }

    public function notificationsFor($userId)
    {
        return $this->morphMany('App\Notification', 'notifiable')->where('user_id', $userId);
    }

    public function messagesFor($userId)
    {
        if(!$this->participants()->where('user_id', $userId)->count())
            return collect([]);
        if($left_at = $this->participants()->where('user_id', $userId)->first()->pivot->left_at)
        {
            return $this->messages()->where('created_at', '>', $left_at)->get();
        }
        else
            return $this->messages;
    }

    public function getHasActiveMembersAttribute()
    {
        if($this->participants()->whereNull('left_at')->count())
            return true;

        $first_left_at = $this->participants()->orderBy('left_at', 'asc')->firstOrFail()->pivot->left_at;
        $last_message_sent_at = $this->last_message->created_at;

        if($first_left_at > $last_message_sent_at)
            return false;

        return true;
    }
}
