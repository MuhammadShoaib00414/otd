<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertStripeSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::create([
            'name' => 'stripe_key',
            'value' => '',
        ]);    
        Setting::create([
            'name' => 'stripe_secret',
            'value' => '',
        ]);     
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::whereIn('name', ['stripe_key', 'stripe_secret'])->delete();
    }
}
