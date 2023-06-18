<?php

namespace App\Mail;

use App\EmailNotification;
use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DiscussionReply extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $discussion)
    {
        $this->user = $user;
        $this->discussion = $discussion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(EmailHelper $helper)
    {
        $emailNotification = EmailNotification::find(12);
        $locale = $this->user->locale;
       
        if($emailNotification->is_enabled && $this->user->deleted_at === null) {
            $html = $helper->replaceTagsForUser($emailNotification->htmlWithLocale($locale), $this->user);
            if($this->discussion instanceof \App\Ideation)
                $html = $helper->replaceCtaWith($html, config('app.url').'/ideations/'.$this->discussion->slug);
            else
                $html = $helper->replaceCtaWith($html, config('app.url').'/groups/'.$this->discussion->group->slug.'/discussions/'. $this->discussion->slug);
            $html = $helper->replaceSystemTags($html);
            
            return $this->from(config('mail.from.address'), getsetting('from_email_name', $locale))
                        ->subject($emailNotification->subjectWithLocale($locale))
                        ->html($html);
        }
    }
}
