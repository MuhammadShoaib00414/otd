<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsCancelledColumnToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_cancelled')->default(0);
        });

        DB::table('email_notifications')->insert([
            'name' => 'On Event Cancellation',
            'description'=> 'Email sent to attendees when an event is cancelled.',
            'subject' => 'An event has been cancelled!',
            'is_enabled' => 1,
            'is_editable' => 1,
            'email_html' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
                            <html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>An Event Was Cancelled</title><link href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i" rel="stylesheet"><style type="text/css">#outlook a{padding:0}.ExternalClass{width:100%}.ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,.ExternalClass td,.ExternalClass div{line-height:100%}body,table,td,a{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}table,td{mso-table-lspace:0;mso-table-rspace:0}img{-ms-interpolation-mode:bicubic}img{border:0;outline:none;text-decoration:none}a img{border:none}td img{vertical-align:top}table,table td{border-collapse:collapse}body{margin:0;padding:0;width:100% !important}.mobile-spacer{width:0;display:none}@media all and (max-width:639px){.container{width:100% !important;max-width:600px !important}.mobile{width:auto !important;max-width:100% !important;display:block !important}.mobile-center{text-align:center !important}.mobile-right{text-align:right !important}.mobile-left{text-align:left!important;}.mobile-hidden{max-height:0;display:none !important;mso-hide:all;overflow:hidden}.mobile-spacer{width:auto !important;display:table !important}.mobile-image img {height: auto !important; max-width: 600px !important; width: 100% !important}}</style><!--[if mso]><style type="text/css">body, table, td, a { font-family: Arial, Helvetica, sans-serif !important; }</style><![endif]--></head><body style="font-family: Helvetica, Arial, sans-serif; margin: 0px; padding: 0px; background-color: #f6f6f6;" bgcolor="#f6f6f6"><table style="width: 100%;" class="main" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td style="vertical-align: top; padding: 8px;" valign="top" align="center"><table style="width: 600px;" class="container" width="600" cellspacing="0" cellpadding="0" border="0"><tr><td style="vertical-align: top;" valign="top" align="center"><table style="width: 600px;" class="container header" width="600" cellspacing="0" cellpadding="0" border="0"><tr><td style="vertical-align: top; background-color: #ffffff;" valign="top" bgcolor="#ffffff" align="left"><table style="width: 100%;" class="block" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td style="vertical-align: top; padding: 20px 24px;" valign="top" align="center"><table style="width: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td mc:edit="gw41g9159gn9" style="vertical-align: middle; text-align: center;" valign="middle" align="center"><a target="_blank" href="'.config('app.url').'" style="cursor: pointer; font-size: 0px; line-height: 100%; text-decoration: none;"><img style="margin: 0px; padding: 0px; max-width: 100%; border: medium none;" alt="" src="'.config('app.url').'/logo" width="200" border="0"></a></td></tr></table></td></tr></table></td></tr></table></td></tr></table><table style="width: 100%;" class="container" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td style="padding: 0px; font-size: 2px; line-height: 2px; height: 2px;" height="2">&nbsp;</td></tr></table><table style="width: 600px;" class="container" width="600" cellspacing="0" cellpadding="0" border="0"><tr><td style="vertical-align: top;" valign="top" align="center"><table style="width: 600px;" class="container card" width="600" cellspacing="0" cellpadding="0" border="0"><tr><td style="vertical-align: top; background-color: #ffffff; padding: 40px 20px;" valign="top" bgcolor="#ffffff"><table style="width: 100%;" class="block" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td style="vertical-align: top; padding: 0px 0px 20px;" valign="top" align="left"><table style="width: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td mc:edit="pu6ar0qyjbf9" style="vertical-align: top; font-family: Helvetica, Arial, sans-serif; font-size: 36px; font-weight: bold; line-height: 43px; color: #111113; text-align: center;" class="" valign="top" align="center">New Message<br></td></tr></table></td></tr></table><table style="width: 100%;" class="block" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td style="vertical-align: top;" valign="top" align="center"><table style="width: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td class="" mc:edit="qs2x5pc7dl0j" style="vertical-align: top; font-family: Helvetica, Arial, sans-serif; font-size: 15px; line-height: 22.5px; color: #111113; text-align: center;" valign="top" align="center">
                                                        An event that you RSVP\'d for was cancelled.</td></tr></table></td></tr></table><table style="width: 100%;" class="block" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td style="vertical-align: top; padding: 40px 0px 20px;" valign="top" align="center"><table style="width: auto;" cellspacing="0" cellpadding="0" border="0"><tr><td style="vertical-align: top; text-align: center; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: bold; color: #ffffff; background-color: #f29181; border-radius: 4px;" valign="top" bgcolor="#F29181" align="center"><a class="" target="_blank" mc:edit="dhkk0915x45y" style="display: inline-block; box-sizing: border-box; cursor: pointer; text-decoration: none; margin: 0px; padding: 12px 20px; border: 1px solid #f29181; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: bold; color: #ffffff; background-color: #f29181; border-radius: 4px;" href="@cta">View</a></td></tr></table></td></tr></table></td></tr></table></td></tr></table><table style="width: 100%;" class="container" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td style="padding: 0px; font-size: 2px; line-height: 2px; height: 2px;" height="2">&nbsp;</td></tr></table><table style="width: 600px;" class="container" width="600" cellspacing="0" cellpadding="0" border="0"><tr><td style="vertical-align: top;" valign="top" align="center"><table style="width: 600px;" class="container footer" width="600" cellspacing="0" cellpadding="0" border="0"><tr><td style="vertical-align: top; padding: 20px 24px; background-color: #ffffff;" valign="top" bgcolor="#ffffff" align="left"><table style="width: 100%;" class="block" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td style="vertical-align: top;" valign="top" align="center"><table style="width: 100%;" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td class="" mc:edit="nl19qs8fd5u1" style="vertical-align: top; font-family: Helvetica, Arial, sans-serif; font-size: 12px; line-height: 18px; color: #000000; text-align: center;" valign="top" align="center">
                            Copyright © 2018 On The Dot Connects<br>
                            All rights reserved<br><br>
                            On The Dot Media, Austin, TX 78759
                        </td></tr></table></td></tr></table><table style="width: 100%;" class="block" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td style="vertical-align: top; padding: 20px 0px 0px;" valign="top" align="center"><a style="text-decoration: underline; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: normal; line-height: 21px; color: #2c76ee;" target="_blank" href="#">Unsubscribe</a></td></tr></table></td></tr></table></td></tr></table></td></tr></table></body></html>',
            'email_template' => '<re-html>
                                <re-head>
                                    <re-title>Message Title</re-title>
                                    <re-font href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i"></re-font>
                                </re-head>
                                <re-body background-color="#f6f6f6">
                                    <re-main padding="8px">
                                        <re-container>
                                            <re-header background-color="#ffffff">
                                                <re-block padding="20px 24px" align="center">
                                                    <re-image align="center" href="'.config('app.url').'" src="'.config('app.url').'/logo" width="200"></re-image>
                                                </re-block>
                                            </re-header>
                                        </re-container>
                                        <re-spacer height="2px"></re-spacer>
                                        <re-container>
                                            <re-card background-color="#ffffff" padding="40px 20px">
                                                <re-block padding="0 0 20px 0">
                                                    <re-heading type="h1" align="center">An Event Was Cancelled<br></re-heading>
                                                </re-block>
                                                <re-block align="center">
                                                    <re-text align="center" font-size="15px">
                                                        An event that you RSVP\'d for was cancelled.</re-text>
                                                </re-block>
                                                <re-block align="center" padding="40px 0 20px 0">
                                                    <re-button href="@cta" background-color="#F29181">View</re-button>
                                                </re-block>
                                            </re-card>
                                        </re-container>
                                        <re-spacer height="2px"></re-spacer>
                                        <re-container>
                                            <re-footer padding="20px 24px" background-color="#ffffff">
                                                <re-block align="center">
                                                    <re-text color="#000000" align="center" font-size="12px">
                                                        Copyright © 2018 On The Dot Connects<br>
                                                        All rights reserved<br><br>
                                                        On The Dot Media, Austin, TX 78759
                                                    </re-text>
                                                </re-block>
                                                <re-block align="center" padding="20px 0 0 0">
                                                    <re-link href="#" font-size="12px">Unsubscribe</re-link>
                                                </re-block>
                                            </re-footer>
                                        </re-container>
                                    </re-main>
                                </re-body>
                            </re-html>',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_cancelled');
        });
    }
}
