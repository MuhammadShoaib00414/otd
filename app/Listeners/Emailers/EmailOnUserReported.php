<?php

namespace App\Listeners\Emailers;

use App\Setting;
use App\Events\UserReported;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailOnUserReported
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
     * @param  UserReported  $event
     * @return void
     */
    public function handle(UserReported $event)
    {
        $sendToUser = Setting::where('name', 'technical_assistance_email')->first();
        
    }
}
