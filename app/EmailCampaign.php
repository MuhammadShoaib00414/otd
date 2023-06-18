<?php

namespace App;

use App\Helpers\EmailHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailCampaign extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $dates = [
        'created_at',
        'updated_at',
        'sent_at',
        'send_at',
    ];

    public function getGroupIdsAttribute()
    {
        $sendToDetails = json_decode($this->sent_to_details);

        if (isset($sendToDetails->groups))
            return $sendToDetails->groups;
        else
            return [];
    }

    public function getGroupsAttribute()
    {
        $sendToDetails = json_decode($this->sent_to_details);
        if (isset($sendToDetails->groups)) {
            $groups = collect($sendToDetails->groups)->map(function ($id) {
                return Group::find($id);
            });
        } else {
            $groups = collect();
        }

        return $groups;
    }

    public function getUserIdsAttribute()
    {
        $sendToDetails = json_decode($this->sent_to_details);

        if (isset($sendToDetails->users))
            return $sendToDetails->users;
        else
            return [];
    }

    public function getUsersAttribute()
    {
        $sendToDetails = json_decode($this->sent_to_details);
        if (isset($sendToDetails->users)) {
            $users = collect($sendToDetails->users)->map(function ($id) {
                return User::find($id);
            });
        } else {
            $users = collect();
        }

        return $users;
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function getSendToUsersAttribute()
    {
        $sendToDetails = json_decode($this->sent_to_details);
        if (isset($sendToDetails->groups)) {
            $users = User::join('group_user', 'group_user.user_id', '=', 'users.id');
            $users = $users->whereIn('group_id', $this->groupIds);
        } 
        if (isset($sendToDetails->users)) {
            $users = User::whereIn('users.id', $this->userIds);
        }
                   
        return $users->distinct('id')->get();
    }

    public function getTotalUsersAttribute()
    {
        return $this->sendToUsers->count();
    }

    public function send()
    {
        $this->status = 'sending';
        $this->save();
        $helper = new EmailHelper();
        
        foreach ($this->sendToUsers as $user) {
            if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                \Mail::html(
                    $helper->replaceColors($this->email_html),
                    function ($message) use ($user) {
                        $message = $message->subject($this->email_subject)
                                ->to($user->email);
                        if($this->reply_to_email)
                            $message = $message->replyTo($this->reply_to_email);
                    }
                );
                 $this->total_sent += 1;
                 $this->save();
            }
        }

        $this->sent_at = \Carbon\Carbon::now()->toDateTimeString();
        $this->status = 'sent';
        $this->save();
    }
}
