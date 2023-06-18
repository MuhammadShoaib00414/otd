<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $guarded = ['id'];

    protected $dates = [
        'created_at',
        'updated_at',
        'start_at',
        'end_at',
    ];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function getIsLiveAttribute()
    {
        if ($this->is_enabled) {
            $start = $this->start_at ? $this->start_at : Carbon::now()->subHours(1);
            $end = $this->end_at ? $this->end_at : Carbon::now()->addHours(1);
            if (Carbon::now()->between($start, $end))
                return true;
        }
        return false;
    }
}
