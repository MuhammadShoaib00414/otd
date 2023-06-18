<?php

use App\Tutorial;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTutorialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tutorials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->timestamps();
        });

        Tutorial::create([
            'name' => 'Group Settings',
            'url' => 'https://kb.onthedotglobal.com/knowledge-base/group-admin/managing-group-settings/'
        ]);

        Tutorial::create([
            'name' => 'Ideations',
            'url' => 'https://kb.onthedotglobal.com/knowledge-base/users/ideations/',
        ]);

        Tutorial::create([
            'name' => 'Email Campaigns',
            'url' => 'https://kb.onthedotglobal.com/knowledge-base/group-admin/sending-email-campaigns/',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tutorials');
    }
}
