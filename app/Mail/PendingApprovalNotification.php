<?php

namespace App\Mail;

use App\EmailNotification;
use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PendingApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $emailNotification;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->emailNotification = EmailNotification::find(20);
        
    }

    /**
     * Specify the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['mail'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    
    public function build(EmailHelper $helper)
    {
      
        if ($this->emailNotification->is_enabled) {
            $locale = $this->user->locale;
            $html = $helper->replaceTagsForUser($this->emailNotification->htmlWithLocale($locale), $this->user);
            $html = $helper->replaceCtaWith($html, config('app.url').'/notifications');
            $html = $helper->replaceNotificationsWithFeed($html, $this->user);
            $html = $helper->replaceSystemTags($html);

            return $this->from(config('mail.from.address'), getsetting('from_email_name', $locale))
                        ->subject($this->emailNotification->subjectWithLocale($locale))
                        ->html($html);
        }

        // Provide a default return statement here if necessary
    }
}
