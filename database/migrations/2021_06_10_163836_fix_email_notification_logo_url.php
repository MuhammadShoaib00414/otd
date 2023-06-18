<?php

use App\EmailNotification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixEmailNotificationLogoUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach(EmailNotification::all() as $email)
        {
            $template = str_replace('http://otd-diversity.test', config('app.url'), $email->email_template);
            $html = str_replace('http://otd-diversity.test', config('app.url'), $email->email_html);

            $email->update([
                'email_html' => $html,
                'email_template' => $template,
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
