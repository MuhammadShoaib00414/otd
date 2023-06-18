<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TextPost extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'custom_menu' => 'array',
        'localization' => 'array'
    ];
    

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function listing()
    {
        return $this->morphOne(Post::class, 'post');
    }

    public function notifications()
    {
        return $this->morphMany(\App\Notification::class, 'notifiable');
    }

    public function notificationsFor($userId)
    {
        return $this->morphMany(\App\Notification::class, 'notifiable')->where('user_id', $userId);
    }
    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function getCustomLinksAttribute()
    {
        if(getsetting('is_localization_enabled') && isset($this->custom_menu) && is_array($this->custom_menu))
        {
            $custom_links = [];
            $count = 0;
            $locale = \Illuminate\Support\Facades\App::getLocale();

            foreach($this->custom_menu as $link)
            {
                $hasLocalizedValue = isset($this->localization[$locale]['links'][$count]['title']);
                $parsedLink['title'] = $hasLocalizedValue ? $this->localization[$locale]['links'][$count]['title'] : $link['title'];
                $parsedLink['title_es'] = isset($this->localization['es']['links'][$count]['title']) ? $this->localization['es']['links'][$count]['title'] : '';
                $parsedLink['url'] = $link['url'];
                $custom_links[] = $parsedLink;
                $count++;
            }

            return $custom_links;
        }
        return $this->custom_menu;
    }
}
