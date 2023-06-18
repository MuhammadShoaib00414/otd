<?php

namespace App\Jobs\Exports\Sheets\Chunkers;

use App\Taxonomy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Exports\Sheets\TaxonomySheet;
use App\Jobs\Exports\UpdateCompletedExport;

class TaxonomyChunker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user_ids;
    private $folder;
    private $segment;
    private $export;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_ids, $folder, $segment, $export)
    {
        $this->user_ids = $user_ids;
        $this->folder = $folder;
        $this->segment = $segment;
        $this->export = $export;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach(Taxonomy::where('is_enabled', 1)->cursor() as $taxonomy)
        {
            $isFirst = true;
            foreach($this->user_ids->chunk(30) as $chunked_user_ids)
            {
                TaxonomySheet::dispatch($chunked_user_ids, $this->folder, $isFirst, $taxonomy);
                $isFirst = false;
            }
        }

        UpdateCompletedExport::dispatch($this->export, $this->segment, $this->folder);
    }
}
