<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Group;
use App\Ideation;

class File extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'localization' => 'array',
    ];

    public function getUrlAttribute()
    {
        if($this->group_id)
            return "/groups/{$this->group->slug}/files/{$this->id}/download";
        else
            return "/ideations/{$this->ideation->slug}/files/{$this->id}/download";
    }

    public function getNameAttribute($value)
    {
        return localizedValue('name', $this->localization) ?: $value;
    }

    public function getPathAttribute($path)
    {
        return getS3Url($path);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function ideation()
    {
        return $this->belongsTo(Ideation::class);
    }
}
