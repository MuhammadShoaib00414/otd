<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertIsStripeEnabledSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::create([
            'name' => 'is_stripe_enabled',
            'value' => 0,
        ]);

        Setting::create([
            'name' => 'superpower_prompt',
            'value' => 'My superpower is...',
        ]);

        Setting::create([
            'name' => 'summary_prompt',
            'value' => 'Make a great first impression',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::whereIn('name', ['is_stripe_enabled', 'superpower_prompt', 'summary_prompt'])->delete();
    }
}
