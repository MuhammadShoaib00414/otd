<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertInstanceSettingsToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::create([
            'name' => 'is_management_chain_enabled',
            'value' => 1,
        ]);
        Setting::create([
            'name' => 'is_departments_enabled',
            'value' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::where('name', 'is_management_chain_enabled')->delete();
        Setting::where('name', 'is_departments_enabled')->delete();
    }
}
