<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportedUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reported_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('reported_by')->index();
            $table->text('reason');
            $table->enum('status', ['reported', 'blocked'])->default('reported');
            $table->timestamps();

            
        });

        Schema::table('reported_users', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('reported_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reported_users');
    }
}
