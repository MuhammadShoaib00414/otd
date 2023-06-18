<?php

use App\Tutorial;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTutorials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Tutorial::where('name', 'Personal Dashboard')->update([
            'url' => 'https://kb.onthedotglobal.com',
        ]);

        Tutorial::create([
            'name' => 'Group Header and Thumbnails',
            'url' => 'https://kb.onthedotglobal.com/knowledge-base/group-admin/71/',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Tutorial::where('name', 'Group Header and Thumbnails')->delete();

        Tutorial::where('name', 'Personal Dashboard')->update([
            'url' => 'https://kb.onthedotglobal.com/knowledge-base/users/introduction-to-your-personal-dashboard',
        ]);
    }
}
