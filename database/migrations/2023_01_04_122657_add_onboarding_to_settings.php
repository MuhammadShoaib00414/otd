<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnboardingToSettings extends Migration
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
                ['name' => 'is_name_lable', 'value' => ''],
                ['name' => 'is_location', 'value' => ''],
                ['name' => 'is_location_required', 'value' => ''],
                ['name' => 'is_gender', 'value' => ''],
                ['name' => 'is_gender_required', 'value' => ''],
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
            Schema::dropIfExists('settings');
        });
    }
}
