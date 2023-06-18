<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email_subject')->nullable();
            $table->longText('email_html')->nullable();
            $table->longText('email_template')->nullable();
            $table->integer('created_by_user')->unsigned()->nullable();
            $table->foreign('created_by_user')->references('id')->on('users');
            $table->timestamp('sent_at')->nullable();
            $table->integer('sent_by_user')->unsigned()->nullable();
            $table->foreign('sent_by_user')->references('id')->on('users');
            $table->text('sent_to_details')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('email_campaigns');
    }
}
