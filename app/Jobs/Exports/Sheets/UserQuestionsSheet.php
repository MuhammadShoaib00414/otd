<?php

namespace App\Jobs\Exports\Sheets;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Exports\Sheets\UsersQuestionsSheet;

class UserQuestionsSheet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $folder;
    private $user_ids;
    private $isFirst;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_ids, $folder, $isFirst)
    {
        $this->user_ids = $user_ids;
        $this->folder = $folder;
        $this->isFirst = $isFirst;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new UsersQuestionsSheet($this->user_ids, $this->folder, $this->isFirst));
    }
}
