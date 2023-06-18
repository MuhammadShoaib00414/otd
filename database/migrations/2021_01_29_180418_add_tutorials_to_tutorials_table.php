<?php

use App\Tutorial; 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTutorialsToTutorialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Tutorial::create([
            'name' => 'Posting Content',
            'url' => 'https://kb.onthedotglobal.com/knowledge-base/users/adding-content-to-your-community-or-event/',
        ]);

        Tutorial::create([
            'name' => 'Video Rooms',
            'url' => 'https://kb.onthedotglobal.com/knowledge-base/users/one-on-one-video-conferencing/',
        ]);

        Tutorial::create([
            'name' => 'Personal Dashboard',
            'url' => 'https://kb.onthedotglobal.com/knowledge-base/users/introduction-to-your-personal-dashboard',
        ]);

        Tutorial::create([
            'name' => 'Shoutouts',
            'url' => 'https://kb.onthedotglobal.com/knowledge-base/users/what-is-a-shoutout-2/',
        ]);

        Tutorial::create([
            'name' => 'Inviting & Managing Users',
            'url' => 'https://kb.onthedotglobal.com/knowledge-base/platform-administration/inviting-and-managing-users/',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Tutorial::whereIn('name', ['Posting Content', 'Video Rooms', 'Personal Dashboard', 'Shoutouts', 'Inviting & Managing Users'])->delete();
    }
}
