<?php
use App\User;
use App\PushNotification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPushNotificationsForCommentPost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
  

        $push_notifications = [
            [
                'name' => 'New Comment',
                'title' => 'New Comment',
                'body' => 'From @userName',
                'tags' => [
                    '@userName' => 'The user who sent the Comment.',
                ],
            ],
        
        ];

        foreach($push_notifications as $push_notification)
            PushNotification::create($push_notification);

       

       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
