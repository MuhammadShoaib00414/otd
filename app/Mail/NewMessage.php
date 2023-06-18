<?php

namespace App\Mail;

use App\User;
use App\EmailNotification;
use App\Notification;
use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewMessage extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Notification $notification)
    {
        $this->user = $user;
        $this->notification = $notification;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(EmailHelper $helper)
    {
        $emailNotification = EmailNotification::find(2);
        $locale = $this->user->locale;
        if($emailNotification->is_enabled && $this->user->deleted_at === null) {
            $html = $helper->replaceTagsForUser($emailNotification->htmlWithLocale($locale), $this->notification->notifiable->last_message->author);
            $html = $helper->replaceCtaWith($html, config('app.url').'/messages');
            $html = $helper->replaceSystemTags($html);
            
            return $this->from(config('mail.from.address'), getsetting('from_email_name', $locale))
                        ->subject($emailNotification->subjectWithLocale($locale))
                        ->html($html);
        }
    }
}
