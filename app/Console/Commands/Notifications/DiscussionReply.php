<?php

namespace App\Console\Commands\Notifications;

use App\User;
use App\Group;
use App\DiscussionPost;
use App\DiscussionThread;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Events\Discussions\DiscussionReplied;

class DiscussionReply extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discussion:reply {--user=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $group = factory(Group::class)->create();

        $group->users()->sync($userId);

        $discussion = DiscussionThread::create([
            'name' => randomString(10),
            'slug' => Str::slug(randomString(10), '-'),
            'group_id' => $group->id,
            'user_id' => $createdBy->id,
        ]);

        DiscussionPost::create([
            'body' => randomString(10),
            'discussion_thread_id' => $discussion->id,
            'user_id' => $userId,
        ]);

        DiscussionPost::create([
            'body' => randomString(10),
            'discussion_thread_id' => $discussion->id,
            'user_id' => $createdBy->id
        ]);

        event(new DiscussionReplied($createdBy, $discussion, $group));

        return $this->info('Notification triggered.');
    }
}
