<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLinkHrefTargetColumnToClickAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('click_areas', function (Blueprint $table) {
            $table->string('a_target')->default('_blank');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('click_areas', function (Blueprint $table) {
            $table->dropColumn('a_target');
        });
    }
}
