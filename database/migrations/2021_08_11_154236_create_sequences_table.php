<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSequencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sequences', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->foreignId('sequence_id')->unsigned()->nullable();
            $table->foreign('sequence_id')->references('id')->on('sequences');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['sequence_id']);
            $table->dropColumn('sequence_id');
        });
        Schema::dropIfExists('sequences');
    }
}
