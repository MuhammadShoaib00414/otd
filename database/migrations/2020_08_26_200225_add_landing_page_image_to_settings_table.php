<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLandingPageImageToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::insert("INSERT INTO `settings` (`name`, `value`, `created_at`, `updated_at`) VALUES
                ('home_page_image', '/images/hands-option-2.png', '2020-08-25 20:26:20', '2020-08-25 20:26:20');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Setting::where('name', 'home_page_image')->delete();
    }
}
