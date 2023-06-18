<?php

namespace App\Jobs\Exports\Sheets;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Exports\Sheets\UsersActivitySheet;

class ActivitySheet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $folder;
    public $log_ids;
    private $isFirst;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($log_ids, $folder, $isFirst)
    {
        $this->log_ids = $log_ids;
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
        (new UsersActivitySheet($this->log_ids, $this->folder, $this->isFirst));
    }
}
