<?php

namespace App\Observers;

use App\Shoutout;

class ShoutoutObserver
{
    public function deleted(Shoutout $shoutout)
    {
        if($shoutout->listing()->exists())
        	$shoutout->listing()->delete();
    }
}
