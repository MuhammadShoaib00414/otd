<?php

namespace App\Mail;

use App\EmailNotification;
use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewArticle extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $article)
    {
        $this->user = $user;
        $this->article = $article;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(EmailHelper $helper)
    {
        $emailNotification = EmailNotification::find(17);
        $locale = $this->user->locale;

        if($emailNotification->is_enabled && $this->user->deleted_at === null) {

            $html = $helper->replaceTagsForUser($emailNotification->htmlWithLocale($locale), $this->user);
            $html = $helper->replaceCtaWith($html, config('app.url').'/groups/'.$this->article->listing->group->slug.'/posts/'. $this->article->listing->id);
            $html = $helper->replaceSystemTags($html);
            
            return $this->from(config('mail.from.address'), getsetting('from_email_name', $locale))
                        ->subject($emailNotification->subjectWithLocale($locale))
                        ->html($html);
        }
    }
}
