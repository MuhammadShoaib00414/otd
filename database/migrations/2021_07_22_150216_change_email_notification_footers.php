<?php

use App\EmailNotification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeEmailNotificationFooters extends Migration
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
            $templateFooter = "Copyright © @year On The Dot<br>All rights reserved </re-text>";
            $template = preg_replace("/(Copyright)[\s\S]*(re-block>)/", $templateFooter, $email->email_template);

            $html = $email->email_html;
            $htmlFooter = "Copyright © @year On The Dot<br>All rights reserved";

            $html = preg_replace("/(Copyright)[\s\S]*(78759)/", $htmlFooter, $html);

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
