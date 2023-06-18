<?php

namespace App\Console\Commands\Notifications;

use App\User;
use App\Group;
use App\Event;
use Illuminate\Console\Command;

class EventCancelled extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:cancel {--user=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a dummy notification to --user';

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
        
        $group = factory(Group::class)->create();

        $group->users()->sync($userId);

        $user = User::find($userId);

        $userCreated = User::where('id', '!=', $userId)->first();

        $event = $this->createEvent($userCreated, $group);

        event(new \App\Events\EventCancelled([$user], $event));

        $group->delete();

        return $this->info('Notification triggered');
    }

    public function createEvent($userCreated, $group)
    {
        return Event::create([
            'name' => randomString(10),
            'date' => \Carbon\Carbon::now()->subMinutes(5),
            'end_date' => \Carbon\Carbon::now(),
            'description' => randomString(10),
            'allow_rsvps' => 0,
            'created_by' => $userCreated->id,
            'group_id' => $group->id,
            'max_participants' => 1,
        ]);
    }
}
