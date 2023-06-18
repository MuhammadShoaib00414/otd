<?php

namespace App\Sms;

use App\User;
use Twilio\Rest\Client;

class SmsNotification
{
    public function __construct(User $user)
    {
        $this->user = $user;

        $this->send();
    }

    public function send()
    {
        $sid    = env( 'TWILIO_SID' );
        $token  = env( 'TWILIO_TOKEN' );
        $client = new Client( $sid, $token );
        $notificationCount = $this->user->notifications()->whereNull('viewed_at')->whereNull('sent_at')->count();
        $message = 'You have ' . $notificationCount . ' new ' . str_plural('notifications', $notificationCount) . '. Click here to view: ' . config('app.url') . '/notifications';

        //dont send text if an unviewed notification was sent in the last hour.
        if($this->user->phone && $this->user->notifications()->whereNull('viewed_at')->whereNull('sent_at')->count()) {
            $client->messages->create(
                $this->user->phone,
                [
                    'from' => env('TWILIO_FROM'),
                    'body' => $message,
                ]
            );
            $this->user->notifications()->whereNull('viewed_at')->whereNull('sent_at')->update(['sent_at' => \Carbon\Carbon::now()]);
        }
    }
}
