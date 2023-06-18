<?php

namespace App;

use App\Traits\UserTimezoneAware;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Message extends Model
{
    use UserTimezoneAware, Cachable;
    
    protected $fillable = ['sending_user_id', 'message','subject', 'recipient_read_at'];

    public function author()
    {
        return $this->belongsTo(User::class, 'sending_user_id');
    }

    public function thread()
    {
        return $this->belongsTo(MessageThread::class, 'message_thread_id');
    }

    public function getSentAtAttribute()
    {
        return $this->getDateToUserTimezone($this->created_at);
    }

    public function getFormattedBodyAttribute()
    {
        $body = $this->message;
        // check if body has tags
        if (!preg_match('/<[^>]*>/', $body)) {
            $url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
            $body = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $body);
            $body = nl2br($body);
        }

        return $body;
    }
}
