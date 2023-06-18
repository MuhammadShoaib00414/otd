<?php

namespace App\Mail;

use App\EmailNotification;
use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewEvent extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $calendarEvent)
    {
        $this->user = $user;
        $this->calendarEvent = $calendarEvent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(EmailHelper $helper)
    {
        $emailNotification = EmailNotification::find(9);
        if ($this->user->deleted_at === null) {

            if (!$emailNotification->is_enabled)
                return false;

            $otdEvent = $this->calendarEvent;
            $locale = $this->user->locale;

            $html = $helper->replaceTagsForUser($emailNotification->htmlWithLocale($locale), $this->user);
            $html = $helper->replaceCtaWith($html, config('app.url') . '/groups/' . $otdEvent->group->slug . '/events/' . $otdEvent->id);
            $html = $helper->replaceSystemTags($html);

            return $this->from(config('mail.from.address'), getsetting('from_email_name', $locale))
                ->subject($emailNotification->subjectWithLocale($locale))
                ->html($html);
        }
    }
}
