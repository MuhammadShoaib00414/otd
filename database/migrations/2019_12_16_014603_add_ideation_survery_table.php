<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdeationSurveryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ideation_surveys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('image_url');
            $table->text('url');
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
        Schema::dropIfExists('ideation_surveys');
    }
}
