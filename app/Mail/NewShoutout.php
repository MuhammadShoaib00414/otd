<?php

namespace App\Mail;

use App\Post;
use App\User;
use App\Shoutout;
use App\EmailNotification;
use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewShoutout extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Post $post)
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
        $emailNotification = EmailNotification::find(4);
        $locale = $this->user->locale;
        
        if($emailNotification->is_enabled && $this->user->deleted_at === null) {
            $html = $helper->replaceTagsForUser($emailNotification->htmlWithLocale($locale), $this->user);
            if($this->post->group()->exists() || $this->post->groups()->exists())
                $html = $helper->replaceCtaWith($html, config('app.url').'/groups/'.$this->post->groups()->first()->slug.'/shoutouts');
            else
                $html = $helper->replaceCtaWith($html, config('app.url').'/shoutouts/received');
            $html = $helper->replaceSystemTags($html);
            
            return $this->from(config('mail.from.address'), getsetting('from_email_name', $locale))
                        ->subject($emailNotification->subjectWithLocale($locale))
                        ->html($html);
        }
    }
}
