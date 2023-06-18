<?php

namespace App\Jobs;

use App\User;
use App\Introduction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\MessageSent;
use App\MessageThread;

class SendMessages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $recipients;
    private $sentByUser;
    private $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recipients, $sentByUser, $data)
    {
        $this->recipients = $recipients;
        $this->sentByUser = $sentByUser;
        $this->data = collect($data);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->recipients as $recipient)
            $this->createThread($recipient);
    }

    private function createThread($users)
    {
        $user = $this->sentByUser;
        $recipients = collect([$user->id])->merge($users)->unique();

        $recipients = $recipients->map(function ($recipientId) {
            return User::find($recipientId);
        });
        // Check if there's an existing thread for these participants
        $participantIds = $recipients->pluck('id');

        $threads = $user->threads()->wherePivotIn('user_id', $participantIds)->has('participants', count($participantIds))->get();

        if ($threads->count())
        {
            $threads = $threads->filter(function ($thread) use ($recipients) {
                return $thread->participants()->pluck('user_id')->sort()->values() == $recipients->pluck('id')->sort()->values();
            });
            $thread = $threads->last();
            if ($thread) {
                if($this->data->contains('event'))
                {
                    $thread = MessageThread::create([
                        'event_id' => $this->data['event'],
                        'type' => $this->data['type'],
                    ]); 
                }
            } else {
                $thread = MessageThread::create(); 
                $thread->participants()->saveMany($recipients);
            }
        } else {
            $thread = MessageThread::create();
            $thread->participants()->saveMany($recipients);
        }
        $message = $thread->messages()->create([
            'sending_user_id' => $user->id,
            'message' => $this->data['message'],
        ]);

        if(Introduction::participants($recipients)->count())
        {
            $introduction = Introduction::participants($recipients)->first();
            $are_messages_sent = json_decode($introduction->are_messages_sent, true);
            $are_messages_sent[$user->id] = 1;
            $introduction->update(['are_messages_sent' => json_encode($are_messages_sent)]);
        }

        if(collect($users)->count() == 1 && $this->data->has('is_from_mentor') && !$user->points()->where('key', 'find-mentor')->count())
        {
            $user->awardPoint('find-mentor');
        }

        //for good measure
        $thread->touch();
        \Log::info('before Message Sent.', ['users' => $user]);
        event(new MessageSent($user, $thread, $message));
        \Log::info('after Message Sent.', ['users' => $user]);
        if($user->threads()->count() >= 5)
            $user->badges()->syncWithoutDetaching(4);

        return $thread;
    }
}
