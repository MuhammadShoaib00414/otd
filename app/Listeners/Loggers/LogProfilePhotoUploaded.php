<?php

namespace App\Listeners\Loggers;

use App\User;
use App\Events\ProfilePhotoUploaded;

class LogProfilePhotoUploaded
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
     * @param  \App\Events\OrderShipped  $event
     * @return void
     */
    public function handle(ProfilePhotoUploaded $event)
    {
        $event->user->logs()->create([
            'action'             => 'profile photo uploaded',
            'related_model_type' => User::class,
            'related_model_id'   => $event->user->id,
        ]);
    }
}