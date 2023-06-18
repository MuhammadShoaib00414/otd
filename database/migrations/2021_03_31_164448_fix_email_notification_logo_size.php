<?php

use App\EmailNotification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixEmailNotificationLogoSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $emails = EmailNotification::whereIn('id', [5,6,7])->get();

        foreach($emails as $email)
        {
            $email->update([
                'email_template' => str_replace('width="200"></re-image>', 
                    'width="70"></re-image>', $email->email_template),
                'email_html' => str_replace('width="200"', 'width="70"', $email->email_html),
            ]);
        }
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
