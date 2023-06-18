<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventIdToMessageThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('message_threads', function (Blueprint $table) {
            $table->integer('event_id')->unsigned()->nullable();
            $table->string('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('message_threads', function (Blueprint $table) {
            $table->dropColumn('event_id');
            $table->dropColumn('type');
        });
    }
}
