<?php

namespace App;

use Mail;
use App\Mail\InviteUser;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $dates = [
        'sent_at',
        'accepted_at',
        'revoked_at',
        'last_sent_at',
    ];

    protected $casts = [
        'add_to_groups' => 'array'
    ];

    public function send()
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL))
            return;

        Mail::to($this->email)->send(new InviteUser($this));
        $this->last_sent_at = \Carbon\Carbon::now();
        $this->save();
    }
    
}
