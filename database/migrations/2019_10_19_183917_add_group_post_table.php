<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_post', function (Blueprint $table) {
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->integer('post_id')->unsigned();
            $table->foreign('post_id')->references('id')->on('posts');
        });

        foreach(\App\Post::all() as $post) {
            \DB::table('group_post')->insert([
                'group_id' => $post->group_id,
                'post_id' => $post->id,
            ]);
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->integer('group_id')->unsigned()->nullable();
            $table->foreign('group_id')->references('id')->on('groups');
        });

        Schema::dropIfExists('group_post');
    }
}
