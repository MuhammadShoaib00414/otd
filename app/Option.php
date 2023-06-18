<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Option extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['id'];

    protected $casts = [
        'localization' => 'array',
    ];

    public function taxonomy()
    {
        return $this->belongsTo(Taxonomy::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->disableCache();
    }

    public function activeUsers()
    {
        return $this->belongsToMany(User::class)->where('is_enabled', '=', '1')->distinct('id')->whereNotNull('job_title')->where('is_hidden', '=', '0');
    }

    public function getIconAttribute()
    {
        if(!$this->taxonomy->is_badge)
            return '';
        if($this->icon_url)
            return getS3Url($this->icon_url);
        else
            return '/images/badge-default.svg';
    }

    public function getQueryAttribute()
    {
        $filters['options'] = request()->options;
        if (isset($filters['options'])) {
            if (in_array($this->id, $filters['options']))
                $filters['options'] = array_diff($filters['options'], [$this->id]);
            else
                $filters['options'] = array_merge($filters['options'], [$this->id]);
        } else {
            $filters['options'] = [$this->id];
        }
        if (request()->has('group')) {
            $filters['group'] = request()->group;
        }

        return http_build_query($filters);
    }

    public function getIsBadgeAttribute()
    {
        return $this->taxonomy()->pluck('is_badge');
    }

    public function parentOrderKey($key)
    {
        return (count(explode('-', $this[$key.'_order_key'])) == 2) ? explode('-', $this[$key.'_order_key'])[0] : null;
    }

    public function orderKey($key)
    {
        return (count(explode('-', $this[$key.'_order_key'])) == 2) ? explode('-', $this[$key.'_order_key'])[1] : null;
    }

    public function getNameAttribute($value)
    {
        return localizedValue('name', $this->localization) ?: $value;
    }

    public function getParentAttribute($value)
    {
        return localizedValue('parent', $this->localization) ?: $value;
    }
}
