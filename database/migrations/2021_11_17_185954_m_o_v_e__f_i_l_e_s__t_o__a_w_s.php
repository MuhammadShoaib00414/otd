<?php

use App\ArticlePost;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\MountManager;

class MOVEFILESTOAWS extends Migration
{
    // the following will collect all files in the old disks and move them to AWS.
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $prefix = public_path() . '/uploads/';
        if(!Storage::exists($prefix))
            return;

        $files = collect(File::allFiles($prefix));

        foreach($files as $file){
            $dir_array = explode('/', $file->getRelativePathname());
            $filename = array_pop($dir_array);
            $dir = implode('/', $dir_array);

            Storage::disk('s3')->putFileAs($dir, $file->getPathname(), $filename);

            // Storage::disk($from)->deleteDirectory($directory);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
