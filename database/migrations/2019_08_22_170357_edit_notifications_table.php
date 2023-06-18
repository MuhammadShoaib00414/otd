<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_notifications', function (Blueprint $table) {
            $table->dropColumn('body');
            $table->longText('email_html')->nullable();
            $table->longText('email_template')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_notifications', function (Blueprint $table) {
            $table->dropColumn(['email_html', 'email_template']);
            $table->longText('body')->nullable();
        });
    }
}
