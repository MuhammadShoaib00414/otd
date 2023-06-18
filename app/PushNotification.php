<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    protected $fillable = [
        'name',
        'title',
        'body',
        'tags',
        'is_enabled',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public $timestamps = false;
    public function send($user, $notification)
    {
        if (!$this->is_enabled)
            return;
        $title = $this->replaceTags($this->title, $notification, $user);
        $body = $this->replaceTags($this->body, $notification, $user);
        $notificationCount = $user->unreadNotifications()->count();
        if ($user->enable_push_notifications);
        $activeDevices = $user->devices->where('active', true);
        foreach ($activeDevices as $device) {
            sendPushNotification($device->token, $title, $body, $notificationCount, $device->device_type);
        }
    }

    public function replaceTags($string, $notification, $user)
    {
        // there's probably a better way of doing this
        if ($notification->notes != null && array_key_exists('commented_by', $notification->notes)) {
            $commentedBy = User::find($notification->notes['commented_by']);
            $string = str_replace('@userName', $commentedBy->name, $string);
            $string = str_replace('@username', $commentedBy->name, $string);
        } 
        else {
         
            $string = str_replace('@userName', $notification->notifiable->user->name, $string);
            $string = str_replace('@username', $notification->user->name, $string);
        }

        if ($this->id == 1)
            return str_replace('@sender', $notification->notifiable->last_message->author->name, $string);
        else if ($this->id == 2) {
            $string = str_replace('@introducedBy', $notification->notifiable->invitee->name, $string);
            return str_replace('@introducedTo', $notification->notifiable->otherUser($user->id)->name, $string);
        } else if ($this->id == 3){
            $string = str_replace('@shouter', $notification->notifiable->shouting->name, $string);
            $group =  $notification->notifiable->listing->group->name;
            $name = $group ? $group : ' a group';
            return str_replace('@groupName', $name, $string);
        }
            //return str_replace('@shouter', $notification->notifiable->shouting->name, $string);
        else if ($this->id == 4)
            return str_replace('@eventName', $notification->notifiable->name, $string);
        else if ($this->id == 5)
            return str_replace('@eventName', $notification->notifiable->name, $string);
        else if ($this->id == 6) {
            $string = str_replace('@reporter', User::find($notification->notes['reported_by'])->name, $string);
            $group = ($notification->notifiable instanceof \App\DiscussionThread) ? $notification->notifiable->group : $notification->notifiable->getGroupFromUser($user->id);
            $name = $group ? $group->name : ' a group';
            return str_replace('@groupName', $name, $string);
        } else if ($this->id == 7) {
            $string = str_replace('@eventName', $notification->notifiable->name, $string);
            $eventName =  $notification->notifiable->name;
            $group =  $notification->notifiable->listing->group->name;
            $name = $group ? $group : ' a group';
            $data = str_replace('@groupName', $name, $string);
            return str_replace('@eventName', $eventName, $data);


        } else if ($this->id == 8) {
            $string = str_replace('@userName', $notification->notifiable->user()->first()->name, $string);
            $group =$notification->notifiable->listing->group->name;
            $name = $group ? $group : ' a group';
            return str_replace('@focusGroup', $name, $string);
        } else if ($this->id == 9) {
            $string = str_replace('@focusGroup', $notification->notifiable->name, $string);
            $discussionName =  $notification->notifiable->name;
            $group =  $notification->notifiable->listing->group->name;
            $name = $group ? $group : ' a group';
            $data = str_replace('@groupName', $name, $string);
            return str_replace('@discussionName', $discussionName, $data);

        } else if ($this->id == 10)
            return str_replace('@replier', $notification->notifiable->last_post->owner->name, $string);
        else if ($this->id == 11) {
          
            $string = str_replace('@userName', $notification->notifiable->user->name, $string);
         //   dd( $notification->notifiable->user->name);
            $group = $notification->notifiable->listing->group->name;
            $name = $group ? $group : ' a group';
            return str_replace('@groupName', $name, $string);
        } else if ($this->id == 12) {


            $string = str_replace('@poster',$notification->notifiable->user()->first()->name, $string);
            $group =  $notification->notifiable->listing->getGroupFromUser($user->id);
            $name =  $group ? $group->name : ' a group';
       
            return str_replace('@groupName', $name, $string);
        } else if ($this->id == 13) {
            $user = $notification->notifiable->comments()->first();
            $userId = isset($user->user_id) ? $user->user_id : 0;
            $getuserinfo = User::withTrashed()->where('id', $userId)->first();

            $string = str_replace('@reporter', $getuserinfo->name, $string);
            $group = ($notification->notifiable instanceof \App\DiscussionThread) ? $notification->notifiable->group : $notification->notifiable->getGroupFromUser($user->id);
            $name = $group ? $group->name : ' a group';
            return str_replace('@groupName', $name, $string);
        }
        return $string;
    }
}
