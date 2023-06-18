<?php

namespace App\Jobs\Exports;

use App\User;
use App\Taxonomy;
use Carbon\Carbon;
use App\Exports\SegmentExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Jobs\Exports\Sheets\Chunkers\ActivitySheetChunker;
use App\Jobs\Exports\Sheets\Chunkers\TaxonomyChunker;
use App\Jobs\Exports\Sheets\Chunkers\UserChunker;
use App\Jobs\Exports\Sheets\Chunkers\GroupsChunker;
use App\Jobs\Exports\Sheets\Chunkers\QuestionsSheetChunker;

class SegmentExporter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    private $segment;

    private $folder;

    private $user_ids;
    private $export;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($segment, $folder, $export)
    {
        $this->segment = $segment;
        $this->user_ids = $segment->user_ids;
        $this->export = $export;

        Storage::disk('public_old')->makeDirectory($folder);
        Storage::disk('public_old')->makeDirectory('exports');
        Storage::disk('public_old')->delete(Storage::disk('public_old')->allFiles($folder));
        $this->folder = public_path() . '/uploads/' . $folder;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Each sheet is separated into its own job to maximize how much time each one has to compile
        UserChunker::dispatch($this->user_ids, $this->folder);
        GroupsChunker::dispatch($this->user_ids, $this->folder);
        // this next line is to speed up the questions sheet as much as possible
        $user_ids_with_questions = User::whereHas('questions', function($query) {
            return $query->whereNotNull('answer');
        })->whereIn('users.id', $this->user_ids)->pluck('users.id');
        QuestionsSheetChunker::dispatch($user_ids_with_questions, $this->folder);
        ActivitySheetChunker::dispatch($this->user_ids, $this->folder);

        //very important that this is last, it updates the completed export within this one (it has to be this way to send the email last)
        TaxonomyChunker::dispatch($this->user_ids, $this->folder, $this->segment, $this->export);
    }
}
