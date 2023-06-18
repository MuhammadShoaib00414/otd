<?php

namespace App\Jobs\Exports\Sheets\Chunkers;

use App\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Exports\Sheets\ActivitySheet;

class ActivitySheetChunker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user_ids;
    private $folder;
    private $isFirst;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_ids, $folder)
    {
        $this->user_ids = $user_ids;
        $this->folder = $folder;
        $this->isFirst = true;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::whereIn('user_id', $this->user_ids)->chunk(50, function($chunked_logs) {
            ActivitySheet::dispatch($chunked_logs, $this->folder, $this->isFirst);
            $this->isFirst = false;
        });
    }
}
