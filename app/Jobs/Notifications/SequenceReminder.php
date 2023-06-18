<?php

namespace App\Jobs\Notifications;

use Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SequenceReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Consolidatable;

    public $reminder;
    public $send_after_days;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(\App\SequenceReminder $reminder, $send_after_days)
    {
        $this->reminder = $reminder;
        $this->send_after_days = $send_after_days;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->send_after_days != $this->reminder->send_after_days || !$this->reminder->is_enabled)
            return;

        $emails = $this->reminder->sequence->group->users()->pluck('email');

        foreach($emails as $email)
            Mail::to($email)->send(new \App\Mail\SequenceReminder($this->reminder));
    }
}
