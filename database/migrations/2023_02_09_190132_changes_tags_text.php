<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangesTagsText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('push_notificatioins', function (Blueprint $table) {
            $push_notifications = [
                [
                    'id' => '13',
                    'body' => '@userName just commented on the post in @groupName',
                ],
            ];
            foreach($push_notifications as $push_notification)
                DB::table('push_notifications')->where('id', $push_notification['id'])
                ->update([
                    'body' => $push_notification['body'],
                ]);
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('push_notificatioins', function (Blueprint $table) {
            //
        });
    }
}
