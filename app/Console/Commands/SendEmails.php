<?php

namespace App\Console\Commands;

use App\EmailCampaign;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Mail;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send & process email campaigns';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $campaigns = EmailCampaign::where('status', '=', 'queued')
                                  ->whereNull('send_at')
                                  ->get();

        $campaigns->each(function ($campaign) {
            $campaign->send();
        });

        $now = Carbon::now();

        $campaigns = EmailCampaign::where('status', '=', 'scheduled')
                                  ->whereBetween('send_at', [$now->subSeconds(20)->toDateTimeString(), $now->addSeconds(20)->toDateTimeString()])
                                  ->get();

        $campaigns->each(function ($campaign) {
            $campaign->send();
        });
    }
}
