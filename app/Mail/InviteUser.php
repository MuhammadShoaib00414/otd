<?php

namespace App\Mail;

use App\Invitation;
use App\EmailNotification;
use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InviteUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(EmailHelper $helper)
    {
        $emailNotification = EmailNotification::find(8);
      
            if ($emailNotification->is_enabled || true) {
                $url = config('app.url') . '/invite/' . $this->invitation->hash;
                if ($this->invitation->locale)
                    $url = $url . '?locale=' . $this->invitation->locale;
                $html = $helper->replaceCtaWith($emailNotification->htmlWithLocale($this->invitation->locale), $url);
                $html = $helper->replaceCustomInvitationMessage($html, $this->invitation->custom_message);
                $html = $helper->replaceSystemTags($html);
                $html = $helper->replaceColors($html);

                return $this->from(config('mail.from.address'), getsetting('from_email_name', $this->invitation->locale))
                    ->subject($emailNotification->subjectWithLocale($this->invitation->locale))
                    ->html($html);
            }
      
    }
}
