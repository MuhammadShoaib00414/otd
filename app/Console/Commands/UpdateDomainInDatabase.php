<?php

namespace App\Console\Commands;

use App\ArticlePost;
use App\ClickArea;
use App\DiscussionPost;
use App\EmailCampaign;
use App\EmailNotification;
use App\Group;
use App\IdeationPost;
use App\TextPost;
use Illuminate\Console\Command;

class UpdateDomainInDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updater:domain {replace}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replaces all instances of a given domain with the APP_URL domain.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $replace = $this->argument('replace');

        $emailCampaigns = EmailCampaign::all();
        $emailNotifications = EmailNotification::all();
        $articlePosts = ArticlePost::all();
        $textPost = TextPost::all();
        $clickAreas = ClickArea::all();
        $discussionPosts = DiscussionPost::all();
        $ideationPosts = IdeationPost::all();
        $groups = Group::all();

        $emailCampaigns->each(function ($campaign) use ($replace) {
            $campaign->email_html = str_replace($replace, config('app.url'), $campaign->email_html);
            $campaign->email_template = str_replace($replace, config('app.url'), $campaign->email_template);
            $campaign->save();
        });

        $emailNotifications->each(function ($notification) use ($replace) {
            $notification->email_html = str_replace($replace, config('app.url'), $notification->email_html);
            $notification->email_template = str_replace($replace, config('app.url'), $notification->email_template);
            $notification->save();
        });

        $articlePosts->each(function ($post) use ($replace) {
            $post->image_url = str_replace($replace, config('app.url'), $post->image_url);
            $post->save();
        });

        $textPost->each(function ($post) use ($replace) {
            $post->content = str_replace($replace, config('app.url'), $post->content);
            $post->save();
        });

        $clickAreas->each(function ($area) use ($replace) {
            $area->target_url = str_replace($replace, config('app.url'), $area->target_url);
        });

        $discussionPosts->each(function ($post) use ($replace) {
            $post->body = str_replace($replace, config('app.url'), $post->body);
            $post->save();
        });

        $ideationPosts->each(function ($post) use ($replace) {
            $post->body = str_replace($replace, config('app.url'), $post->body);
            $post->save();
        });

        $groups->each(function ($group) use ($replace) {
            $group->custom_menu = str_replace($replace, config('app.url'), $group->custom_menu);
            $group->save();
        });

    }
}
