<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWorksTableAndColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('works', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('work_offering')->nullable();
            $table->foreign('work_offering')->references('id')->on('works');
            $table->unsignedInteger('work_wanting')->nullable();
            $table->foreign('work_wanting')->references('id')->on('works');
        });

        \App\Work::insert([
            ['name' => 'Mentorship'],
            ['name' => 'Branding'],
            ['name' => 'Leadership Training'],
            ['name' => 'Business Strategy'],
            ['name' => 'Web Development'],
            ['name' => 'Financial Strategy/Advising'],
            ['name' => 'Diversity + Inclusion Strategy'],
            ['name' => 'Interviewing Strategy'],
            ['name' => 'Hiring Strategy'],
            ['name' => 'Confidence Coaching'],
            ['name' => 'Professional Development'],
            ['name' => 'Career Strategy'],
            ['name' => 'Conflict Resolution'],
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
            $table->dropForeign(['work_offering']);
            $table->dropForeign(['work_wanting']);
            $table->dropColumn(['work_offering', 'work_wanting']);
        });
        Schema::dropIfExists('works');
    }
}
