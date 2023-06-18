<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateRangeColumnsToSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('segments', function (Blueprint $table) {
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('segments', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
}
