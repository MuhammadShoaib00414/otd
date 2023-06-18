<?php

namespace App\Mail;

use App\EmailNotification;
use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserReported extends Mailable
{
    use Queueable, SerializesModels;
    
    public $userName;
    public $reportedBy;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($reportedBy, $userName)
    {
        $this->userName = $userName;
        $this->reportedBy = $reportedBy;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(EmailHelper $helper)
    {
        $emailNotification = EmailNotification::where('name', 'On user reported')->first();
        if($emailNotification->is_enabled && $this->user->deleted_at === null) {
            $html = $helper->replaceTagsForReportedBy($emailNotification->email_html, $this->reportedBy->name);
            $html = $helper->replaceTagsForReportedUser($html, $this->userName->name);
            $html = $helper->replaceSystemTags($html);
            return $this->subject($emailNotification->subject)
                        ->html($html);
        }
    }
}


