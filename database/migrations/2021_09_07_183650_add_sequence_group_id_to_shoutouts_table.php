<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSequenceGroupIdToShoutoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoutouts', function (Blueprint $table) {
            $table->integer('sequence_group_id')->nullable()->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shoutouts', function (Blueprint $table) {
            $table->dropColumn('sequence_group_id');
        });
    }
}
