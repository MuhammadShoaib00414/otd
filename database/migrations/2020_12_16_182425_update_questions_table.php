<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->renameColumn('question', 'prompt');
            $table->string('type')->default('text');
            $table->longText('options')->nullable();
            $table->string('visible_when_parent_is')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->renameColumn('prompt', 'question');
            $table->dropColumns(['']);
        });
    }
}
