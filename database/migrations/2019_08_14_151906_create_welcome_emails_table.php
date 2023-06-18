<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWelcomeEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('welcome_emails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email_subject')->nullable();
            $table->longText('email_html')->nullable();
            $table->longText('email_template')->nullable();
            $table->integer('send_after_days')->nullable();
            $table->integer('total_sent')->default(0);
            $table->boolean('enabled')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('welcome_emails');
    }
}
