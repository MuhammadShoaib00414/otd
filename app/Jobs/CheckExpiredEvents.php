<?php

namespace App\Jobs;

use App\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckExpiredEvents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $today = Carbon::now()->startOfDay();
        $events = Event::where('date', '<', $today)->where(function($query) use ($today) {
            return $query->whereNull('end_date')->orWhere('end_date', '<', $today);
        })->get();

        foreach($events as $event)
            $this->updateExpiredEvent($event);
    }

    public function updateExpiredEvent($event)
    {
        $event->notifications()->delete();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    
    }
}
