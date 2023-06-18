<?php

namespace App;

use App\Post;
use Illuminate\Support\Facades\Request;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Illuminate\Database\Eloquent\Model;

class ArticlePost extends Model implements Searchable
{
    protected $guarded = ['id'];

    protected $casts = [
        'localization' => 'array',
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

    public function getSearchResult(): SearchResult
    {     
         return new \Spatie\Searchable\SearchResult(
            $this,
            $this->title,
            $this->url
        );
    }

    public function getImageUrlAttribute($value) 
    {
        return getS3Url($value);
    }

    public function getUrlAttribute($value)
    {
        $listing = $this->listing;
        if(!$listing)
            return '/content/' . $this->id . '/log?' . http_build_query(['next' => $value]);
        $slug = $this->listing->group ?: false;
        if(!$slug && $this->listing && $this->listing->group)
            $slug = $this->listing->group->slug;
        
        if($group = Group::where('slug', $slug)->first())
            return '/groups/'.$group->slug.'/content/' . $this->id . '/log?' . http_build_query(['next' => $value]);
        
        return '/content/' . $this->id . '/log?' . http_build_query(['next' => $value]);
    }

    public function getTitleAttribute($value)
    {
        return localizedValue('title', $this->localization) ?: $value;
    }

    public function getIsVideoAttribute()
    {
        return str_contains($this->url, 'youtube.com') || str_contains($this->url, 'vimeo.com') || str_contains($this->url, 'facebook.com');
    }

    public function getEmbeddedVideoAttribute()
    {
        return $this->generateVideoEmbedUrl($this->getRawOriginal('url'));
    }

    public function getClickCountAttribute()
    {
        return $this->logs()->where('action', 'clicked content')->count();
    }

    public function logs()
    {
        return $this->morphMany('App\Log','secondary_related_model');
    }

    public function generateVideoEmbedUrl($url)
    {
        $finalUrl = '';
        if(strpos($url, 'facebook.com/') !== false) {
            $finalUrl.='https://www.facebook.com/plugins/video.php?href='.rawurlencode($url).'&show_text=1&width=200';
        }else if(strpos($url, 'vimeo.com/') !== false) {
            $videoId = explode("vimeo.com/",$url)[1];
            if(strpos($videoId, '&') !== false){
                $videoId = explode("&",$videoId)[0];
            }
            $finalUrl.='https://player.vimeo.com/video/'.$videoId;
        }else if(strpos($url, 'youtube.com/') !== false && array_key_exists(1, explode("v=", $url))) {
            $videoId = explode("v=",$url)[1];
            if(strpos($videoId, '&') !== false){
                $videoId = explode("&",$videoId)[0];
            }
            $finalUrl.='https://www.youtube.com/embed/'.$videoId;
        }else if(strpos($url, 'youtu.be/') !== false){
            $videoId = explode("youtu.be/",$url)[1];
            if(strpos($videoId, '&') !== false){
                $videoId = explode("&",$videoId)[0];
            }
            $finalUrl.='https://www.youtube.com/embed/'.$videoId;
        }else{
            //Enter valid video URL
        }

        return $finalUrl;
    }
}
