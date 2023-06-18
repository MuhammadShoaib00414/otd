<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\VirtualRoom;
class Lounge extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'localization' => 'array',
    ];

    public function getVirtualRoomAttribute()
    {
        return VirtualRoom::find($this->virtual_room_id);
    }

    public function getMobileVirtualRoomAttribute()
    {
        return VirtualRoom::find($this->mobile_virtual_room_id);
    }

    public function chatRoom()
    {
        return $this->morphOne(ChatRoom::class, 'attachable');
    }

    public function videoRoom()
    {
        return $this->morphOne(VideoRoom::class, 'attachable');
    }

    public function getNameAttribute($value)
    {
        return localizedValue('name', $this->localization) ?: $value;
    }
}
