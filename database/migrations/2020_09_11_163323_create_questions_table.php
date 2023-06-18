<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->integer('created_by')->unsigned();
            $table->boolean('is_enabled')->default(1);
            $table->integer('parent_question_id')->nullable()->unsigned();
            $table->timestamps();
        });

        Schema::create('question_user', function (Blueprint $table) {
            $table->id();
            $table->integer('question_id')->unsigned();
            $table->boolean('answer')->nullable();
            $table->integer('user_id')->unsigned();
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
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_user');
    }
}
