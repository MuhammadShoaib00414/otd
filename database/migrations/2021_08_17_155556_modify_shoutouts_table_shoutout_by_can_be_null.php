<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyShoutoutsTableShoutoutByCanBeNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoutouts', function (Blueprint $table) {
            $table->unsignedInteger('shoutout_by')->nullable()->change();
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
            $table->unsignedInteger('shoutout_by')->nullable(false)->change();
        });
    }
}
