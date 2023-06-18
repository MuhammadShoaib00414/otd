<?php

namespace App\Jobs\Exports\Sheets\Chunkers;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use \App\Jobs\Exports\Sheets\UserGroupsSheet;

class GroupsChunker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user_ids;
    private $folder;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_ids, $folder)
    {
        $this->user_ids = $user_ids;
        $this->folder = $folder;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $isFirst = true;
        foreach($this->user_ids->chunk(50) as $chunked_user_ids)
        {
            UserGroupsSheet::dispatch($chunked_user_ids, $this->folder, $isFirst);
            $isFirst = false;
        }
    }
}
