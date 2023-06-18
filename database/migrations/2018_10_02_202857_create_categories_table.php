<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('category_user', function (Blueprint $table) {
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')
                  ->references('id')->on('categories')->onDelete('cascade');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                  ->references('id')->on('users')->onDelete('cascade');

            $table->primary(['category_id', 'user_id']);
        });

        \App\Category::insert([
            ['name' => 'Accounting'],
            ['name' => 'Beauty'],
            ['name' => 'Coaching'],
            ['name' => 'Consumer Goods & Services'],
            ['name' => 'E-commerce'],
            ['name' => 'Education'],
            ['name' => 'Event Planning'],
            ['name' => 'Family/Parenting'],
            ['name' => 'Fashion '],
            ['name' => 'Financial Services/Banking'],
            ['name' => 'Food & Beverage/Catering'],
            ['name' => 'HR'],
            ['name' => 'Marketing/Public Relations '],
            ['name' => 'Medical Services'],
            ['name' => 'News & Media'],
            ['name' => 'Non-Profits'],
            ['name' => 'Operations'],
            ['name' => 'PR/Advertising'],
            ['name' => 'Sales'],
            ['name' => 'Software'],
            ['name' => 'STEM'],
            ['name' => 'Real Estate'],
            ['name' => 'Recruiting'],
            ['name' => 'Wellness/Healthy Living'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_user');
        Schema::dropIfExists('categories');
    }
}
