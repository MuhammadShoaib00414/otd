<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdeationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ideations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('proposed_by_id')->unsigned();
            $table->foreign('proposed_by_id')->references('id')->on('users');
            $table->integer('max_participants')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('group_ideation', function (Blueprint $table) {
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->integer('ideation_id')->unsigned();
            $table->foreign('ideation_id')->references('id')->on('ideations');
        });

        Schema::create('ideation_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('ideation_id')->unsigned();
            $table->foreign('ideation_id')->references('id')->on('ideations');
        });

        Schema::create('ideation_invitations', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('ideation_id')->unsigned();
            $table->foreign('ideation_id')->references('id')->on('ideations');
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
        Schema::dropIfExists('ideation_invitations');
        Schema::dropIfExists('group_ideation');
        Schema::dropIfExists('ideation_user');
        Schema::dropIfExists('ideations');
    }
}
