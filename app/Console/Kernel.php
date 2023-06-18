<?php

namespace App\Console;

use App\Console\Commands\ClearApcuCache;
use App\Events\CheckExpiredGroups;
use App\Events\NotificationFeed;
use App\Jobs\CheckExpiredEvents;
use App\Jobs\CheckWaitlists;
use App\OTD\DripCampaignSender;
use App\OTD\FeedProcesser;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(new FeedProcesser)->hourly();

        $schedule->call(function () {
            event(new NotificationFeed('daily'));
        })->dailyAt('16:00')->timezone('America/Chicago');

        $schedule->call(function () {
            event(new CheckExpiredGroups());
        })->hourly();
        
        $schedule->call(new DripCampaignSender)->hourly();

        // $schedule->call(new \App\Jobs\CheckExpiredEvents())->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
