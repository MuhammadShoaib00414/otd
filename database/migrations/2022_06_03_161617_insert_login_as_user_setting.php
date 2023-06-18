<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertLoginAsUserSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::create([
            'name' => 'allow_admins_to_login_as_users',
            'value' => 0,
        ]);

        Schema::table('discussion_posts', function (Blueprint $table) {
            $table->longText('body')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::where('name', 'allow_admins_to_login_as_users')->delete();

        Schema::table('discussion_posts', function (Blueprint $table) {
            $table->text('body')->change();
        });
    }
}
