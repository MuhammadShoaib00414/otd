<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class WaitlistedUser extends Model
{
    protected $guarded = ['id'];

    protected $table = 'waitlisted_users';

    public function event()
    {
    	return $this->belongsTo(Event::class);
    }

    public function user()
    {
    	return $this->belongsTo(User::class)->withTrashed();
    }

    public function getEmailAttribute()
    {
        return User::find($this->user_id)->email;
    }
}
