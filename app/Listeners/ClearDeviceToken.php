<?php

namespace App\Listeners;

use App\User;
use Jenssegers\Agent\Agent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClearDeviceToken
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
        $agent = new Agent();
        $device = User::find($event->user->id)->devices
                                    ->where('device_type', $agent->platform())
                                    ->where('device_name', $agent->device() . ', ' . $agent->browser())
                                    ->first();
        if ($device) {
            $device->update([
                'active' => false,
                'inactive_reason' => 'logout'
            ]);
        }
        $event->user->update([
            'device_type' => null,
            'device_token' => null,
        ]);
    }
}
