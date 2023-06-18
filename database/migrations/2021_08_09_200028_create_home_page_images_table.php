<?php

use App\Setting;
use App\HomePageImage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomePageImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_page_images', function (Blueprint $table) {
            $table->id();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });

        HomePageImage::create([
            'image_url' => Setting::where('name', 'home_page_image')->first()->value,
        ]);

        for($i = 0; $i < 3; $i++)
        {
            HomePageImage::create([
                'image_url' => '',
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
        Schema::dropIfExists('home_page_images');
    }
}
