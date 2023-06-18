<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Setting;

class InsertNewMessageTextSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::create([
            'name' => 'new_message_text',
            'value' => 'If this is a medical or mental health crisis, please dial 911. All other messages, please allow up to 24 hours for a response.',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::where('name', 'new_message_text')->delete();
    }
}
