<?php

use App\EmailNotification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailNotificationForReportedUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        EmailNotification::create([
            'name' => 'On user reported',
            'subject' => 'A user has been reported!',
            'description' => 'Email sent to the Technical Assistance of a reported user',
            'is_enabled' => 1,
            'is_editable' => 1,
            'email_html' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Message Title</title><link href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i" rel="stylesheet"><style type="text/css">#outlook a{padding:0}.ExternalClass{width:100%}.ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,.ExternalClass td,.ExternalClass div{line-height:100%}body,table,td,a{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}table,td{mso-table-lspace:0;mso-table-rspace:0}img{-ms-interpolation-mode:bicubic}img{border:0;outline:none;text-decoration:none}a img{border:none}td img{vertical-align:top}table,table td{border-collapse:collapse}body{margin:0;padding:0;width:100% !important}.mobile-spacer{width:0;display:none}@media all and (max-width:639px){.container{width:100% !important;max-width:600px !important}.mobile{width:auto !important;max-width:100% !important;display:block !important}.mobile-center{text-align:center !important}.mobile-right{text-align:right !important}.mobile-left{text-align:left!important;}.mobile-hidden{max-height:0;display:none !important;mso-hide:all;overflow:hidden}.mobile-spacer{width:auto !important;display:table !important}.mobile-image img {height: auto !important; max-width: 600px !important; width: 100% !important}}</style><!--[if mso]><style type="text/css">body, table, td, a { font-family: Arial, Helvetica, sans-serif !important; }</style><![endif]--></head><body bgcolor="#f6f6f6" style="font-family: Helvetica, Arial, sans-serif; margin: 0px; padding: 0px; background-color: #f6f6f6;"><table cellpadding="0" cellspacing="0" border="0" width="100%" class="main" style="width: 100%;"><tr><td align="center" valign="top" style="vertical-align: top; padding: 8px;"><table cellpadding="0" cellspacing="0" border="0" width="600" class="container" style="width: 600px;"><tr><td align="center" valign="top" style="vertical-align: top;"><table cellpadding="0" cellspacing="0" border="0" width="600" class="container header" style="width: 600px;"><tr><td valign="top" align="left" bgcolor="#ffffff" style="vertical-align: top; background-color: #ffffff;"><table cellpadding="0" cellspacing="0" border="0" width="100%" class="block" style="width: 100%;"><tr><td align="center" valign="top" style="vertical-align: top; padding: 20px 24px;"><table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100%;"><tr><td valign="middle" mc:edit="379j3n2ugx4u" align="center" style="vertical-align: middle; text-align: center;"><a target="_blank" href="'.config('app.url').'/" style="cursor: pointer; font-size: 0px; line-height: 100%; text-decoration: none;"><img border="0" alt="" width="200" src="'.config('app.url').'/logo" style="margin: 0px; padding: 0px; max-width: 100%; border: none;"></a></td></tr></table></td></tr></table></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" border="0" width="100%" class="container" style="width: 100%;"><tr><td height="2" style="padding: 0px; font-size: 2px; line-height: 2px; height: 2px;">&nbsp;</td></tr></table><table cellpadding="0" cellspacing="0" border="0" width="600" class="container" style="width: 600px;"><tr><td align="center" valign="top" style="vertical-align: top;"><table cellpadding="0" cellspacing="0" border="0" width="600" class="container card" style="width: 600px;"><tr><td valign="top" bgcolor="#ffffff" style="vertical-align: top; background-color: #ffffff; padding: 40px 20px;"><table cellpadding="0" cellspacing="0" border="0" width="100%" class="block" style="width: 100%;"><tr><td align="left" valign="top" style="vertical-align: top; padding: 0px 0px 20px;"><table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100%;"><tr><td valign="top" mc:edit="hp7dnxeh77z8" class="" align="center" style="vertical-align: top; font-family: Helvetica, Arial, sans-serif; font-size: 36px; font-weight: bold; line-height: 43px; color: #111113; text-align: center;">A user has been reported.</td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" border="0" width="100%" class="block" style="width: 100%;"><tr><td align="center" valign="top" style="vertical-align: top;"><table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100%;"><tr><td class="" valign="top" mc:edit="4kg9bruiikd9" align="center" style="vertical-align: top; font-family: Helvetica, Arial, sans-serif; font-size: 15px; line-height: 22.5px; color: #111113; text-align: center;">@reportedBy has just reported a user @reportedUser!</td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" border="0" width="100%" class="block" style="width: 100%;"><tr><td align="center" valign="top" style="vertical-align: top; padding: 40px 0px 20px;"><table cellpadding="0" cellspacing="0" border="0" style="width: auto;"><tr><td valign="top" bgcolor="#F29181" align="center" style="vertical-align: top; text-align: center; font-family: Helvetica, Arial, sans-serif; font-size: 15px; font-weight: bold; color: #ffffff; background-color: #f29181; border-radius: 4px;"></td></tr></table></td></tr></table></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" border="0" width="100%" class="container" style="width: 100%;"><tr><td height="2" style="padding: 0px; font-size: 2px; line-height: 2px; height: 2px;">&nbsp;</td></tr></table><table cellpadding="0" cellspacing="0" border="0" width="600" class="container" style="width: 600px;"><tr><td align="center" valign="top" style="vertical-align: top;"><table cellpadding="0" cellspacing="0" border="0" width="600" class="container footer" style="width: 600px;"><tr><td valign="top" align="left" bgcolor="#ffffff" style="vertical-align: top; padding: 20px 24px; background-color: #ffffff;"><table cellpadding="0" cellspacing="0" border="0" width="100%" class="block" style="width: 100%;"><tr><td align="center" valign="top" style="vertical-align: top;"><table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100%;"><tr><td class="" valign="top" mc:edit="vlsy91276q6n" align="center" style="vertical-align: top; font-family: Helvetica, Arial, sans-serif; font-size: 12px; line-height: 18px; color: #000000; text-align: center;">
                                        Copyright © 2018 On The Dot Connects<br>
                                        All rights reserved<br><br>
                                        On The Dot Media, Austin, TX 78759
                                    </td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" border="0" width="100%" class="block" style="width: 100%;"><tr><td align="center" valign="top" style="vertical-align: top; padding: 20px 0px 0px;"><a target="_blank" href="#" style="text-decoration: underline; font-family: Helvetica, Arial, sans-serif; font-size: 12px; font-weight: normal; line-height: 21px; color: #2c76ee;">Unsubscribe</a></td></tr></table></td></tr></table></td></tr></table></td></tr></table></body></html>',
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
                                    <re-image align="center" href="'.config('app.url').'/" src="'.config('app.url').'/logo" width="200"></re-image>
                                </re-block>
                            </re-header>
                        </re-container>
                        <re-spacer height="2px"></re-spacer>
                        <re-container>
                            <re-card background-color="#ffffff" padding="40px 20px">
                                <re-block padding="0 0 20px 0">
                                    <re-heading type="h1" align="center">A user has been reported</re-heading>
                                </re-block>
                                <re-block align="center">
                                    <re-text align="center" font-size="15px">@reportedBy has just reported a user @reportedUser!</re-text>
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
        EmailNotification::where('name', 'On user reported')->delete();
    }
}
