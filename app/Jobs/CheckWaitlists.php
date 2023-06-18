<?php

namespace App\Jobs;

use App\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckWaitlists implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $events = Event::all();
        foreach($events as $event)
        {
            if(!$event->has_max_participants && $event->waitlist()->count())
            {
                $event->popWaitlist();
            }
        }
    }
}
