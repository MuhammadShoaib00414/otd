<?php

namespace App\Mail;

use App\EmailNotification;
use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewPost extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $post)
    {
        $this->user = $user;
        $this->post = $post;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(EmailHelper $helper)
    {
        $emailNotification = EmailNotification::find(13);
        $locale = $this->user->locale;

        if($emailNotification->is_enabled && $this->user->deleted_at === null) {
            $group = $this->post->listing->group ?: $this->post->listing->getGroupFromUser($this->user->id);
            $url = config('app.url') . ($group ? '/groups/'.$group->slug.'/posts/'. $this->post->listing->id : '/posts/'.$this->post->listing->id);
            $html = $helper->replaceTagsForUser($emailNotification->htmlWithLocale($locale), $this->user);
            $html = $helper->replaceCtaWith($html, $url);
            $html = $helper->replaceSystemTags($html);
            
            return $this->from(config('mail.from.address'), getsetting('from_email_name', $locale))
                        ->subject($emailNotification->subjectWithLocale($locale))
                        ->html($html);
        }
    }
}
