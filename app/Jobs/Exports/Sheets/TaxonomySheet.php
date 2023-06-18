<?php

namespace App\Jobs\Exports\Sheets;

use App\Taxonomy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Exports\Sheets\UsersTaxonomySheet;

class TaxonomySheet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $folder;
    private $user_ids;
    private $isFirst;
    private $taxonomy;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_ids, $folder, $isFirst, $taxonomy)
    {
        $this->user_ids = $user_ids;
        $this->folder = $folder;
        $this->isFirst = $isFirst;
        $this->taxonomy = $taxonomy;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new UsersTaxonomySheet($this->isFirst, $this->user_ids, $this->folder, $this->taxonomy));
    }
}
