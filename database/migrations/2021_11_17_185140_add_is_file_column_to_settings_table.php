<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsFileColumnToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('is_file')->default(0);
        });

        Setting::whereIn('name', [
            'homepage_image_path',
            'logo_path',
            'logo',
            'home_page_image',
            'dashboard_header_image',
            'dashboard_left_nav_image',
            'pick_registration_image_url',
        ])->update(['is_file' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('is_file');
        });
    }
}
