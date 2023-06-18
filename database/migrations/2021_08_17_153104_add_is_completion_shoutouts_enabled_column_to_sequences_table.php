<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCompletionShoutoutsEnabledColumnToSequencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sequences', function (Blueprint $table) {
            $table->boolean('is_completion_shoutouts_enabled')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sequences', function (Blueprint $table) {
            $table->dropColumn('is_completion_shoutouts_enabled');
        });
    }
}
