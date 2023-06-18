<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutoJoinMessageColumnsToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('is_welcome_message_enabled')->default(0);
            $table->unsignedInteger('welcome_message_sending_user_id')->nullable();
            $table->text('welcome_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn([
                'is_welcome_message_enabled',
                'welcome_message_sending_user_id',
                'welcome_message',
            ]);
        });
    }
}
