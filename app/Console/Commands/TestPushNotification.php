<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class TestPushNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:push {user : The ID of a user} {title?} {body?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a test push notification to a user. userId is required. The user must have their device token set.';

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
     * @return int
     */
    public function handle()
    {
        $user = User::find($this->argument('user'));

        if(!$user)
            return $this->error("User not found.");
        if(!$user->device_token)
            return $this->error("User Device token not set.");

        $title = $this->argument('title') ?: Str::random(10);
        $body = $this->argument('body') ?: Str::random(10);

        return $this->info(sendPushNotification($user->device_token, $title, $body));
        
    }
}
