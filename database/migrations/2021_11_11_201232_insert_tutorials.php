<?php

use App\Tutorial;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertTutorials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Tutorial::create([
            'name' => 'Admin Email Campaigns',
            'url' => 'https://kb.onthedotglobal.com/knowledge-base/platform-administration/how-to-send-email-campaigns/',
        ]);

        Tutorial::create([
            'name' => 'Find Your People',
            'url' => 'https://kb.onthedotglobal.com/knowledge-base/users/what-is-find-your-people/',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Tutorial::whereIn('name', ['Admin Email Campaigns', 'Find Your People'])->delete();
    }
}
