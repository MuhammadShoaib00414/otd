<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertDashboardLeftNavUrlToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::create([
            'name' => 'dashboard_left_nav_image_link',
            'value' => '',
        ]);

        Setting::create([
            'name' => 'does_dashboard_left_nav_image_open_new_tab',
            'value' => '1',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::where('name', 'dashboard_left_nav_image_link')->delete();
        Setting::where('name', 'does_dashboard_left_nav_image_open_new_tab')->delete();
    }
}
