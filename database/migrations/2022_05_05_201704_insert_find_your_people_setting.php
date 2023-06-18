<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertFindYourPeopleSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::create([
            'name' => 'ask_a_mentor_alias',
            'value' => 'Ask a Mentor',
        ]);

        Setting::create([
            'name' => 'find_your_people_alias',
            'value' => 'Find Your People',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::whereIn('name', ['ask_a_mentor_alias', 'find_your_people_alias'])->delete();
    }
}
