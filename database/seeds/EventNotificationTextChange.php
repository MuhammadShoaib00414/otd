<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventNotificationTextChange extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('push_notifications')->where('id');
        DB::table('push_notifications')
        ->where('id',7 )
        ->update(['body' => '@userName just posted a new event "@eventName" in @groupName! RSVP and don\'t miss out!']);
    }
}
 