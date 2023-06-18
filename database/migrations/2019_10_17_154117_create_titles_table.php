<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('titles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('title_id')->unsigned()->nullable();
            $table->foreign('title_id')->references('id')->on('titles');
        });

        \App\Title::insert([
            ['name' => 'Manager'],
            ['name' => 'Second-Line Manager'],
            ['name' => 'Director'],
            ['name' => 'Vice President'],
            ['name' => 'Senior Vice President'],
            ['name' => 'CEO'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['title_id']);
            $table->dropColumn('title_id');
        });
        Schema::dropIfExists('titles');
    }
}
