<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomizableImagePathsToSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('settings')->insert([
            [
                'name' => 'homepage_image_path',
                'value' => '/images/hands-option-2.png',
            ],
            [
                'name' => 'logo_path',
                'value' => '/images/logo-2.png',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::table('settings')->where('name', 'homepage_image_path')->delete();
        \DB::table('settings')->where('name', 'logo_path')->delete();
    }
}
