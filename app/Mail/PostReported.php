<?php

namespace App\Mail;

use App\User;
use App\Post;
use App\Group;
use App\EmailNotification;
use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PostReported extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Post $post, $group)
    {
        $this->user = $user;
        $this->post = $post;
        $this->group = $group;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(EmailHelper $helper)
    {
        $emailNotification = EmailNotification::find(7);

        if($emailNotification->is_enabled && $this->user->deleted_at === null) {
            $html = $helper->replaceTagsForReportedBy($emailNotification->email_html, User::find($this->post->reported_by)->name);
            if($this->group instanceof \App\Group)
                $html = $helper->replaceCtaWith($html, config('app.url').'/groups/'.$this->group->slug.'/posts/'.$this->post->id);
            elseif($this->group instanceof \App\Ideation)
            {
                $articles = $this->post->post instanceof \App\ArticlePost ? '/articles/' : '';
                $html = $helper->replaceCtaWith($html, config('app.url').'/ideations/'.$this->group->slug . $articles . '#'.$this->post->id);
            }
            else
                $html = $helper->replaceCtaWith($html, config('app.url').'/posts/' . $this->post->id);

            if($this->group)
                $html = $helper->replaceGroupNameWith($html, $this->group->name);
            else
                $html = $helper->replaceGroupNameWith($html, getsetting('name'));
            $html = $helper->replaceSystemTags($html);
            $html = $helper->replaceTagsForUserName($html, User::find($this->post->reported_by)->name);
            return $this->subject($emailNotification->subject)
                        ->html($html);
        }
    }
}
