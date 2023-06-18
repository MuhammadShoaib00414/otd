<?php

use App\EmailNotification;
use App\PushNotification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentPostNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        PushNotification::create([
            'name' => 'New Content Post',
            'title' => 'New Content Post ',
            'body' => 'From @poster',
            'tags' => [
                '@poster' => 'The user who posted the content.',
            ],
        ]);
        EmailNotification::create([
            'name' => 'New Content Post',
            'description' => 'Email sent to content post group\'s users',
            'subject' => 'New Content Post',
            'email_template' => '<re-html>
            <re-head>
                <re-title>
                    Message Title
                </re-title>
                <re-font href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i"></re-font>
            </re-head>
            <re-body background-color="#f6f6f6">
                <re-main padding="8px">
                    <re-container>
                        <re-header background-color="#ffffff">
                            <re-block padding="20px 24px" align="center">
                                <re-image align="center" href="'.config('app.url').'" src="'.config('app.url').'/logo" width="70px"></re-image>
                            </re-block>
                        </re-header>
                    </re-container>
                    <re-spacer height="2px"></re-spacer>
                    <re-container background-color="#ffffff" padding="40px 20px">
                            <re-block padding="0 0 20px 0">
                                <re-heading type="h1" align="center" level="h2">New Content Post</re-heading>
                            </re-block>
                            <re-block align="center">
                                <re-text align="center" font-size="15px">
                                </re-text>
                            </re-block>
                            <re-block align="center" padding="40px 0 20px 0">
                                <re-button href="@cta" background-color="#0062CC">Check It Out<br></re-button>
                            </re-block>
                        
                    </re-container>
                    <re-spacer height="2px"></re-spacer>
                    <re-container>
                        <re-footer padding="20px 24px" background-color="#ffffff">
                            <re-block align="center">
                                <re-text color="#000000" align="center" font-size="12px">
                                                                                Copyright © @year On The Dot<br>All rights reserved </re-text>
                        </re-block></re-footer>
                    </re-container>
                </re-main>
            </re-body>
            </re-html>',
            'email_html' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Message Title</title><link href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i" rel="stylesheet"><style type="text/css">#outlook a{padding:0}.ExternalClass{width:100%}.ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,.ExternalClass td,.ExternalClass div{line-height:100%}body,table,td,a{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}table,td{mso-table-lspace:0;mso-table-rspace:0}img{-ms-interpolation-mode:bicubic}img{border:0;outline:none;text-decoration:none}a img{border:none}td img{vertical-align:top}table,table td{border-collapse:collapse}body{margin:0;padding:0;width:100% !important}.mobile-spacer{width:0;display:none}@media all and (max-width:639px){.container{width:100% !important;max-width:600px !important}.mobile{width:auto !important;max-width:100% !important;display:block !important}.mobile-center{text-align:center !important}.mobile-right{text-align:right !important}.mobile-left{text-align:left!important;}.mobile-hidden{max-height:0;display:none !important;mso-hide:all;overflow:hidden}.mobile-spacer{width:auto !important;display:table !important}.mobile-image,.mobile-image img {height: auto !important; max-width: 600px !important; width: 100% !important}}</style><!--[if mso]><style type="text/css">body, table, td, a { font-family: Arial, Helvetica, sans-serif !important; }</style><![endif]--></head><body style="font-family: Helvetica, Arial, sans-serif; margin: 0px; padding: 0px; background-color: #f6f6f6;"><table cellpadding="0" cellspacing="0" border="0" width="100%" class="body" style="width: 100%;"><tbody><tr><td align="center" valign="top" style="vertical-align: top; line-height: 1;"><table cellpadding="0" cellspacing="0" border="0" width="600" class="main container" style="width: 600px;"><tr><td align="left" valign="top" style="vertical-align: top; line-height: 1; padding: 8px;"><table cellpadding="0" cellspacing="0" border="0" width="100%" class="container" style="width: 100%;"><tr><td align="left" valign="top" style="vertical-align: top; line-height: 1;"><table cellpadding="0" cellspacing="0" border="0" width="600" class="header container" style="width: 600px;"><tr><td align="left" valign="top" bgcolor="#ffffff" style="vertical-align: top; line-height: 1; background-color: #ffffff;"><table cellpadding="0" cellspacing="0" border="0" width="100%" class="block" style="width: 100%;"><tr><td align="center" valign="top" style="vertical-align: top; line-height: 1; padding: 20px 24px;"><span style="display: inline-block; font-size: 0px; line-height: 0; vertical-align: top;" class=""><a href="'.config('app.url').'" target="_blank" style="font-family: Helvetica, Arial, sans-serif; font-size: 0px; font-weight: normal; color: #0091ff; text-decoration: none; cursor: pointer; line-height: 100%; display: block;"><img border="0" width="70" src="'.config('app.url').'/logo" style="margin: 0px; padding: 0px; max-width: 100%; border: none; vertical-align: top; width: 70px;"></a></span></td></tr></table></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" border="0" width="100%" class="spacer" style="width: 100%;"><tr><td align="left" valign="top" style="vertical-align: top; line-height: 2px; padding: 0px; font-size: 2px; height: 2px;">&nbsp;</td></tr></table><table cellpadding="0" cellspacing="0" border="0" width="100%" class="container" style="width: 100%;"><tr><td align="left" valign="top" bgcolor="#ffffff" style="vertical-align: top; line-height: 1; padding: 40px 20px; background-color: #ffffff;"><table cellpadding="0" cellspacing="0" border="0" width="100%" class="block" style="width: 100%;"><tr><td align="left" valign="top" style="vertical-align: top; line-height: 1; padding: 0px 0px 20px;"><h2 class="h2" align="center" style="padding: 0px; margin: 0px; font-style: normal; font-family: Helvetica, Arial, sans-serif; font-size: 28px; line-height: 37px; color: #111118; font-weight: bold;">
                                    New Content Post</h2></td></tr></table><table cellpadding="0" cellspacing="0" border="0" width="100%" class="block" style="width: 100%;"><tr><td align="center" valign="top" style="vertical-align: top; line-height: 1;"><p align="center" style="padding: 0px; margin: 0px; font-family: Helvetica, Arial, sans-serif; color: #222228; font-size: 15px; line-height: 23px;">
                                </p></td></tr></table><table cellpadding="0" cellspacing="0" border="0" width="100%" class="block" style="width: 100%;"><tr><td align="center" valign="top" style="vertical-align: top; line-height: 1; padding: 40px 0px 20px;"><table cellpadding="0" cellspacing="0" border="0" width="auto" style="width: auto; font-size: 18px; font-weight: normal; background-color: #0062cc; color: #ffffff; border-radius: 24px; border-collapse: separate;" class=""><tr><td align="center" valign="top" bgcolor="#0062CC" style="vertical-align: top; line-height: 1; text-align: center; font-family: Helvetica, Arial, sans-serif; border-radius: 24px;"><a class="" target="_blank" href="@cta" style="display: inline-block; box-sizing: border-box; cursor: pointer; text-decoration: none; margin: 0px; font-family: Helvetica, Arial, sans-serif; font-size: 18px; font-weight: normal; background-color: #0062cc; color: #ffffff; border-radius: 24px; border: 1px solid #0062cc; padding: 14px 40px;">Check It Out<br>
                                </a></td></tr></table></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" border="0" width="100%" class="spacer" style="width: 100%;"><tr><td align="left" valign="top" style="vertical-align: top; line-height: 2px; padding: 0px; font-size: 2px; height: 2px;">&nbsp;</td></tr></table><table cellpadding="0" cellspacing="0" border="0" width="100%" class="container" style="width: 100%;"><tr><td align="left" valign="top" style="vertical-align: top; line-height: 1;"><table cellpadding="0" cellspacing="0" border="0" width="600" class="footer container" style="width: 600px;"><tr><td align="left" valign="top" bgcolor="#ffffff" style="vertical-align: top; line-height: 1; padding: 20px 24px; background-color: #ffffff;"><table cellpadding="0" cellspacing="0" border="0" width="100%" class="block" style="width: 100%;"><tr><td align="center" valign="top" style="vertical-align: top; line-height: 1;"><p align="center" style="padding: 0px; margin: 0px; font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 12px; line-height: 18px;">
                                                                                Copyright © @year On The Dot<br>All rights reserved </p></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></tbody></table></body></html>',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_post_notification');
    }
}
