<?php

namespace App\Mail;

use App\EmailNotification;
use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;
class NewComment extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $notifiable;
    public $authUser;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $notifiable, $authUser)
    {

        $this->user = $user;   
        $this->notifiable = $notifiable;
        $this->authUser = $authUser;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(EmailHelper $helper)
    {
       
        $comment = DB::table('comments')->where('user_id',$this->authUser->id)->latest()->first();
       
        $emailNotification = EmailNotification::where('name', 'User send comments on post')->first();
        if($emailNotification->is_enabled && $this->user->deleted_at === null) {

            $html = $helper->replaceTagsForUserName($emailNotification->email_html, $this->authUser->name);
            $html = $helper->replaceTagsForComment($html, $comment->text);
            $html = $helper->replaceSystemTags($html);
            return $this->subject($emailNotification->subject)
                        ->html($html);
        }
    }
}
