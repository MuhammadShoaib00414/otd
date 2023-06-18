<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IdeationInvitation extends Model
{
    protected $guarded = ['id'];

    protected $dates = ['read_at'];

    protected $primaryKey = null;
    
    public $incrementing = false;

    public function ideation()
    {
    	return $this->belongsTo(App\Ideation::class, 'ideation_id');
    }

    public function sent_by()
    {
    	return $this->belongsTo(App\User::class, 'sent_by_id');
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
