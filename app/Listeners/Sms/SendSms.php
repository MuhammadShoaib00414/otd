<?php

namespace App\Listeners\Sms;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSms
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {

        $notificationCount = $event->user->notifications()->whereNull('viewed_at')->whereNull('sent_at')->count();
        $message = 'You have ' . $notificationCount . ' new ' . str_plural('notifications', $notificationCount) . '. Click here to view: ' . config('app.url') . '/notifications';
        $sid    = config('otd.plivo_sid');
        $token  = config('otd.plivo_token');
        $client = new \Plivo\Resources\PHLO\PhloRestClient( $sid, $token );
        $phlo = $client->phlo->get("9ced1863-39ef-425f-9540-29508da358a7");
        if($event->user->phone && $event->user->notifications()->whereNull('viewed_at')->whereNull('sent_at')->count()) {
            try {
                $response = $phlo->run([
                    'From' => config('otd.plivo_from'), 
                    'To' => $event->user->phone,
                    'Body' => $message,
                ]);
            } catch (\Plivo\Exceptions\PlivoRestException $ex) {
                return false;
            }
            $event->user->notifications()->whereNull('viewed_at')->whereNull('sent_at')->update(['sent_at' => \Carbon\Carbon::now()]);
        }
    }
}
