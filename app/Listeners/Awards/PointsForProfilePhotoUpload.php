<?php

namespace App\Listeners\Awards;

use App\Point;
use App\AwardedPoint;
use App\Events\ProfilePhotoUploaded;

class PointsForProfilePhotoUpload
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
     * @param  \App\Events\IntroductionMade  $event
     * @return void
     */
    public function handle(ProfilePhotoUploaded $event)
    {
        $event->user->awardPoint('upload-photo');
    }
}