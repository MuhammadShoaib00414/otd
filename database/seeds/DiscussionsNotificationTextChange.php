<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscussionsNotificationTextChange extends Seeder
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
        ->where('id',9 )
        ->update(['body' => '@userName just started a new discussion  "@discussionName" in @groupName! Join in and share your thoughts.']);
    }
}

