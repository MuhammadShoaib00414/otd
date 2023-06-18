<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $existingVal = getSetting('is_quick_onboarding_enabled');
        Setting::create([
            'name' => 'is_superpower_enabled',
            'value' => $existingVal,
        ]);
        Setting::create([
            'name' => 'is_about_me_enabled',
            'value' => $existingVal,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::where('name', 'is_about_me_enabled')
               ->orWhere('name', 'is_superpower_enabled')
               ->delete();
    }
}
