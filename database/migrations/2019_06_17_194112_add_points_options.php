<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPointsOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Point::insert([
            [
                'key'         => 'make-shoutout',
                'name'        => 'Shoutout a Colleague',
                'description' => 'Awarded when a user creates a shoutout',
                'value'       => 2
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
