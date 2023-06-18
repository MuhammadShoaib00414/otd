<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDefaultNewMessageText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::where('name', 'new_message_text')->where('value', 'If this is a medical or mental health crisis, please dial 911. All other messages, please allow up to 24 hours for a response.')->update(['value' => '']);   
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
