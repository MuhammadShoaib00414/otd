<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportedPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reported_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('postable_type');
            $table->integer('postable_id')->unsigned();
            $table->integer('reported_by')->unsigned();
            $table->integer('resolved_by')->unsigned()->nullable();
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
        Schema::dropIfExists('reported_posts');
    }
}
