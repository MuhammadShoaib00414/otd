<?php

use App\HomePageImage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLangColumnToHomePageImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('home_page_images', function (Blueprint $table) {
            $table->string('lang')->default('en');
        });

        for($i = 0; $i < 4; $i++)
        {
            HomePageImage::create([
                'image_url' => '',
                'lang' => 'es',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('home_page_images', function (Blueprint $table) {
            $table->dropColumn('lang');
        });
    }
}
