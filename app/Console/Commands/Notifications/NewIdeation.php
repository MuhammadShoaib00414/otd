<?php

namespace App\Console\Commands\Notifications;

use App\User;
use App\Group;
use App\Ideation;
use App\Events\Ideations\NewIdeation as NewIdeationEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class NewIdeation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ideation:new {--user=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a dummy new ideation notification to a given user.';

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

        $createdBy = User::where('id', '!=', $userId)->first();

        $ideation = Ideation::create([
            'name' => randomString(10),
            'slug' => Str::slug(Str::random(10), '-'),
            'user_id' => $createdBy->id,
            'proposed_by_id' => $createdBy->id,
            'is_approved' => 1,
        ]);

        $ideation->update([
            'slug' => Str::slug(Str::random(10), '-') . "-" . $ideation->id
        ]);

        $ideation->invitations()->create(['user_id' => $userId, 'sent_by_id' => $createdBy->id]);

        event(new NewIdeationEvent($createdBy, $ideation));

        return $this->info('Notification triggered.');
    }
}
