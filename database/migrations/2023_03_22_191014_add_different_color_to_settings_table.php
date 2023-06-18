<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDifferentColorToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            DB::table('settings')->insert([
                ['name' => 'admin_btn_secondary', 'value' => ''],
                ['name' => 'admin_btn_success', 'value' => ''],
                ['name' => 'admin_btn_danger', 'value' => ''],
                ['name' => 'admin_btn_warning', 'value' => ''],
                ['name' => 'admin_btn_info', 'value' => ''],
                ['name' => 'admin_btn_dark', 'value' => ''],
                ['name' => 'admin_btn_light', 'value' => ''],
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
          
        });
    }
}
