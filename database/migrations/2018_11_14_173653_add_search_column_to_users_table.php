<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSearchColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('search')->nullable();
        });

        $users = \App\User::get();

        foreach ($users as $user) {
            $keywords = implode(' ', $user->keywords->pluck('name')->toArray());
            $categories = implode(' ', $user->categories->pluck('name')->toArray());
            $user->search = $keywords . ' ' . $categories;
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('search');
        });
    }
}
