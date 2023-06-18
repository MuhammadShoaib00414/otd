<?php

namespace App;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class WelcomeEmail extends Model
{
    protected $guarded = ['id'];

    public function getRecipientsAttribute()
    {
        $start = Carbon::now()->subDays($this->send_after_days)->subHours(1);
        $end = Carbon::now()->subDays($this->send_after_days);
        $users = User::whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()])->get();
        return $users;
    }

    public function send()
    {
        foreach ($this->recipients as $user) {
             \Mail::html($this->email_html, function ($message) use ($user) {
                $message->subject($this->email_subject)
                        ->to($user->email);
            });
             $this->total_sent += 1;
             $this->save();
        }
    }
}
