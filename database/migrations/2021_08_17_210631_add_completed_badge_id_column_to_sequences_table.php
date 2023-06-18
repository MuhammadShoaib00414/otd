<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompletedBadgeIdColumnToSequencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sequences', function (Blueprint $table) {
            $table->unsignedInteger('completed_badge_id')->nullable();
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
            $table->dropColumn('completed_badge_id');
        });
    }
}
