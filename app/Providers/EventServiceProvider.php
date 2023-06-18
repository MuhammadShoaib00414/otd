<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Logout' => [
            'App\Listeners\ClearDeviceToken',
        ],
        'Illuminate\Auth\Events\Registered' => [
            'App\Listeners\Loggers\LogSignup',
            'App\Listeners\Awards\PointsForSignup',
            'App\Listeners\Emailers\EmailOnSignup'
        ],
        'App\Events\UserSignedin' => [
            'App\Listeners\Loggers\LogSignin',
            'App\Listeners\AwardPointsForSignin',
        ],
        'App\Events\PaymentProcessed' => [
            'App\Listeners\Emailers\EmailReceipt',
        ],
        'App\Events\MessageSent' => [
            'App\Listeners\Loggers\LogMessageSent',
            'App\Listeners\Awards\PointsForMessaging',
            'App\Listeners\Notifications\NewMessage',
        ],
        'App\Events\IntroductionMade' => [
            'App\Listeners\Loggers\LogIntroduction',
            'App\Listeners\Awards\PointsForIntroduction',
            'App\Listeners\Notifications\NewIntroduction',
        ],
        'App\Events\ProfileUpdated' => [
            'App\Listeners\Loggers\LogProfileUpdate',
        ],
        'App\Events\SearchEvent' => [
            'App\Listeners\Loggers\LogSearch',
        ],
        'App\Events\ViewProfile' => [
            'App\Listeners\Loggers\LogProfileView',
        ],
        'App\Events\NewPost' => [
            'App\Listeners\Notifications\NewPost',
            'App\Listeners\Loggers\LogPostMade',
        ],
        'App\Events\NewArticle' => [
            'App\Listeners\Notifications\NewArticle',
        ],
        'App\Events\ShoutoutMade' => [
            'App\Listeners\Loggers\LogShoutout',
            'App\Listeners\Awards\PointsForShoutout',
            'App\Listeners\Notifications\NewShoutout',
        ],
        'App\Events\EventCreated' => [
            'App\Listeners\Loggers\LogEventCreated',
            'App\Listeners\Notifications\NewEvent',
        ],
        'App\Events\EventViewed' => [
            'App\Listeners\Loggers\LogEventViewed',
        ],
        'App\Events\RsvpChanged' => [
            'App\Listeners\Loggers\LogRsvpChanged',
            'App\Listeners\Awards\PointsForRSVP'
        ],
        'App\Events\IdeationProposed' => [
            'App\Listeners\Notifications\NotifyGroupAdminsIdeationProposed',
        ],
        'App\Events\Ideations\NewIdeation' => [
            'App\Listeners\Loggers\Ideations\LogIdeationMade',
            'App\Listeners\Awards\PointsForNewIdeation',
            'App\Listeners\Notifications\NewIdeation',
        ],
        'App\Events\Ideations\IdeationViewed' => [
            'App\Listeners\Loggers\Ideations\LogIdeationViewed',
            'App\Listeners\Awards\PointsForIdeationViewed',
        ],
        'App\Events\Ideations\IdeationReplied' => [
            'App\Listeners\Loggers\Ideations\LogIdeationReplied',
            'App\Listeners\Awards\PointsForIdeationReplied',
            'App\Listeners\Notifications\IdeationReply',
        ],
        'App\Events\Ideations\IdeationInvite' => [
            'App\Listeners\Loggers\Ideations\LogIdeationInvite',
            'App\Listeners\Notifications\IdeationInvite',
            'App\Listeners\Awards\PointsForIdeationInvite',
        ],
        'App\Events\Ideations\IdeationDeleted' => [
            'App\Listeners\Loggers\Ideations\LogIdeationDeleted',
        ],
        'App\Events\Discussions\NewDiscussion' => [
            'App\Listeners\Loggers\Discussions\LogDiscussionMade',
            'App\Listeners\Notifications\NewDiscussion',
            'App\Listeners\Awards\PointsForNewDiscussion',
        ],
        'App\Events\Discussions\DiscussionPostReported' => [
            'App\Listeners\Notifications\DiscussionPostReported',
        ],
        'App\Events\Discussions\DiscussionReplied' => [
            'App\Listeners\Loggers\Discussions\LogDiscussionReply',
            'App\Listeners\Notifications\DiscussionReply',
            'App\Listeners\Awards\PointsForDiscussionReply',
        ],
        'App\Events\Discussions\DiscussionEdit' => [
            'App\Listeners\Loggers\Discussions\LogDiscussionEdit',
        ],
        'App\Events\Discussions\DiscussionDeleted' => [
            'App\Listeners\Loggers\Discussions\LogDiscussionDeleted',
        ],
        'App\Events\Budgets\BudgetViewed' => [
            'App\Listeners\Loggers\Budgets\LogBudgetViewed',
            'App\Listeners\Awards\PointsForBudgetViewed',
        ],
        'App\Events\Budgets\ExpenseSaved' => [
            'App\Listeners\Loggers\Budgets\LogExpenseSaved',
            'App\Listeners\Awards\PointsForExpenseSaved',
        ],
        'App\Events\Budgets\ExpenseUpdated' => [
            'App\Listeners\Loggers\Budgets\LogExpenseUpdated',
        ],
        'App\Events\Budgets\ExpenseDeleted' => [
            'App\Listeners\Loggers\Budgets\LogExpenseDeleted',
        ],
        'App\Events\ProfilePhotoUploaded' => [
            'App\Listeners\Loggers\LogProfilePhotoUploaded',
            'App\Listeners\Awards\PointsForProfilePhotoUpload',
        ],
        'App\Events\LeftWaitlist' => [
            'App\Listeners\Notifications\LeftWaitlist',
        ],
        'App\Events\EventCancelled' => [
            'App\Listeners\Notifications\EventCancelled',
        ],
        'App\Events\PostReported' => [
            'App\Listeners\Emailers\EmailOnPostReported',
            'App\Listeners\Notifications\PostReported',
        ],
        'App\Events\NotificationFeed' => [
            'App\Listeners\Emailers\EmailNotificationFeed',
        ],
        'App\Events\SmsNotification' => [
            'App\Listeners\Sms\SendSms',
        ],
        'App\Events\UserReported' => [
            'App\Listeners\Notifications\UserReported',
        ],
        'App\Events\NewComment' => [
            'App\Listeners\Notifications\NewComment',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        \App\Post::observe(\App\Observers\PostObserver::class);
    }
}
