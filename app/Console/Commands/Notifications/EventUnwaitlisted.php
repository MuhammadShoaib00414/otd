<?php

namespace App\Console\Commands\Notifications;

use App\User;
use App\Group;
use App\Event;
use Illuminate\Console\Command;

class EventUnwaitlisted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:waitlist {--user=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a dummy notification simulating someone getting off a waitlist.';

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
        $userId = $this->option('user');

        if(!$userId)
            return $this->error('--user field is required');
        
        $group = Group::first();

        $group->users()->syncWithoutDetaching($userId);

        $user = User::find($userId);

        $userCreated = User::where('id', '!=', $userId)->first();

        $event = $this->createEvent($userCreated, $group);

        $event->waitlist()->create([
            'user_id' => $userId,
        ]);

        $event->popWaitlist();

        return $this->info('Notification triggered');
    }

    public function createEvent($userCreated, $group)
    {
        return Event::create([
            'name' => randomString(10),
            'date' => \Carbon\Carbon::now()->subMinutes(5),
            'end_date' => \Carbon\Carbon::now(),
            'description' => randomString(10),
            'allow_rsvps' => 1,
            'created_by' => $userCreated->id,
            'group_id' => $group->id,
            'max_participants' => 1,
        ]);
    }
}
