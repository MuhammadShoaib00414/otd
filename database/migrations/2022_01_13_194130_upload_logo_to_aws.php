<?php

use App\ArticlePost;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\MountManager;

class UploadLogoToAws extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $prefix = public_path() . '/images/';
        $files = collect(File::allFiles($prefix));

        foreach($files as $file){
            $dir_array = explode('/', $file->getRelativePathname());
            $filename = array_pop($dir_array);
            $dir = '/images/';

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
