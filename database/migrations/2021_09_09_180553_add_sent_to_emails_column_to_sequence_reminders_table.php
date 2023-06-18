<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSentToEmailsColumnToSequenceRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sequence_reminders', function (Blueprint $table) {
            $table->longText('sent_to_emails')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sequence_reminders', function (Blueprint $table) {
            $table->dropColumn('sent_to_emails');
        });
    }
}
