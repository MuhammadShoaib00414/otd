<?php

namespace App\Mail;

use Mail;
use App\EmailNotification;
use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewIdeation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $ideation)
    {
        $this->user = $user;
        $this->ideation = $ideation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(EmailHelper $helper)
    {
        $emailNotification = EmailNotification::find(10);
        if ($this->user->deleted_at === null) {

            if (!$emailNotification->is_enabled)
                return false;

            $locale = $this->user->locale;

            $html = $helper->replaceTagsForUser($emailNotification->htmlWithLocale($locale), $this->user);
            $html = $helper->replaceCtaWith($html, config('app.url') . '/ideations/invited');
            $html = $helper->replaceSystemTags($html);

            return $this->from(config('mail.from.address'), getsetting('from_email_name', $locale))
                ->subject($emailNotification->subjectWithLocale($locale))
                ->html($html);
        }
    }
}
