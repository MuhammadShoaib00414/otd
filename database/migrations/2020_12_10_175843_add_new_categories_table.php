<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxonomies', function ($table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_enabled')->default(1);
            $table->boolean('is_public')->default(1);
            $table->boolean('is_user_editable')->default(1);
            $table->boolean('is_visible_in_group_admin_reporting')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('options', function ($table) {
            $table->bigIncrements('id');
            $table->bigInteger('taxonomy_id')->unsigned();
            $table->foreign('taxonomy_id')->references('id')->on('taxonomies');
            $table->string('name')->nullable();
            $table->string('parent')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->boolean('is_enabled')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('option_user', function ($table) {
            $table->bigInteger('option_id')->unsigned();
            $table->foreign('option_id')->references('id')->on('options');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('option_user');
        Schema::dropIfExists('options');
        Schema::dropIfExists('taxonomies');
    }
}
