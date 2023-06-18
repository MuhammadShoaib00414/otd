<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAreMessagesSentColumnToIntroductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('introductions', function (Blueprint $table) {
            $table->text('are_messages_sent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('introductions', function (Blueprint $table) {
            $table->dropColumn('are_messages_sent');
        });
    }
}
