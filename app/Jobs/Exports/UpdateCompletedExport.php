<?php

namespace App\Jobs\Exports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UpdateCompletedExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $export;

    public $segment;

    public $folder;

    public $filename;

    public $segmentName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($export, $segment, $folder)
    {
        $this->export = $export;
        $this->segment = $segment;
        $this->folder = $folder;

        $this->segmentName = str_replace('/', '', $this->segment->name);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->export->update([
            'path' => base_path() . '/public/uploads/exports/export - ' . $this->segment->getFolderSafeName() . '.zip',
        ]);

        $this->zipContents();

        $this->export->send();
    }

    public function zipContents()
    {
        // Create a list of files that should be added to the archive.
        $files = glob($this->folder . '/*');

        $zipPath = public_path() . '/uploads/exports/';

        if(!Storage::disk('public_old')->exists($zipPath)) {
            Storage::disk('public_old')->makeDirectory($zipPath, 0775, true); //creates directory
        }

        // Define the name of the archive and create a new ZipArchive instance.
        $archiveFile = $zipPath . 'export - ' . $this->segment->getFolderSafeName() . '.zip';
        $archive = new \ZipArchive();

        // Check if the archive could be created.
        if (! $archive->open($archiveFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            throw new Exception("Zip file could not be created: ".$archive->getStatusString());
        }

        // Loop through all the files and add them to the archive.
        foreach ($files as $file) {
            if (! $archive->addFile($file, basename($file))) {
                throw new Exception("File [`{$file}`] could not be added to the zip file: ".$archive->getStatusString());
            }
        }

        // Close the archive.
        if (! $archive->close()) {
            throw new Exception("Could not close zip file: ".$archive->getStatusString());
        }
    }
}
