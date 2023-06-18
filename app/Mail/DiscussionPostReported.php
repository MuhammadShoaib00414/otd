<?php

namespace App\Mail;

use App\EmailNotification;
use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DiscussionPostReported extends Mailable
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
        $emailNotification = EmailNotification::find(7);
        $locale = $this->user->locale;

        if($emailNotification->is_enabled && $this->user->deleted_at === null) {
            $html = $helper->replaceTagsForUser($emailNotification->htmlWithLocale($locale), $this->user);
            $html = $helper->replaceCtaWith($html, config('app.url').'/groups/'.$this->discussion->group->slug.'/discussions/'. $this->discussion->slug);
            $html = $helper->replaceSystemTags($html);
            $html = $helper->replaceGroupNameWith($html, $this->discussion->group->name);
            //$html = $helper->replaceTagsForUserName($html, $this->event->reported_by_name);
            
            return $this->from(config('mail.from.address'), getsetting('from_email_name', $locale))
                        ->subject($emailNotification->subjectWithLocale($locale))
                        ->html($html);
        }
    }
}
