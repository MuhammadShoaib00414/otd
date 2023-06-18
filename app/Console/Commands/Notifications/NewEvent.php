<?php

namespace App\Console\Commands\Notifications;

use App\User;
use App\Group;
use App\Event;
use App\Events\EventCreated;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class NewEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:new {--user=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a dummy event and notifies a given user in an optional group.';

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

        $userCreated = User::where('id', '!=', $userId)->first();

        $event = $this->createEvent($userCreated, $group);

        event(new EventCreated($userCreated, $event));

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
