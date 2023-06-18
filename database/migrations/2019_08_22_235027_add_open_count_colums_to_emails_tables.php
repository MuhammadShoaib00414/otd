<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOpenCountColumsToEmailsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_notifications', function(Blueprint $table) {
            $table->integer('open_total')->nullable();
        });
        Schema::table('email_campaigns', function(Blueprint $table) {
            $table->integer('open_total')->nullable();
        });
        Schema::table('welcome_emails', function(Blueprint $table) {
            $table->integer('open_total')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_notifications', function(Blueprint $table) {
            $table->dropColumn('open_total');
        });
        Schema::table('email_campaigns', function(Blueprint $table) {
            $table->dropColumn('open_total');
        });
        Schema::table('welcome_emails', function(Blueprint $table) {
            $table->dropColumn('open_total');
        });
    }
}
