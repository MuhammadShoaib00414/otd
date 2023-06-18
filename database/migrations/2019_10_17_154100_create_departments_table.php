<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('department_id')->unsigned()->nullable();
            $table->foreign('department_id')->references('id')->on('departments');
        });

        \App\Department::insert([
            ['name' => 'Sales'],
            ['name' => 'Marketing'],
            ['name' => 'Finance'],
            ['name' => 'I/T'],
            ['name' => 'R&D'],
            ['name' => 'HR'],
            ['name' => 'Operations'],
            ['name' => 'Purchasing'],
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
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
        Schema::dropIfExists('departments');
    }
}
