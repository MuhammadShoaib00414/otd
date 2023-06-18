<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\User;

class AddIsOnboardedColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_onboarded')->default(0);
        });

        User::chunk(50, function($users) {
            foreach($users as $user)
            {
                if(($user->location || $user->job_title) && $user->groups()->count())
                {
                    $user->update([
                        'is_onboarded' => 1,
                    ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_onboarded');
        });
    }
}
