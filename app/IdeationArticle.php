<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IdeationArticle extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'localization' => 'array',
    ];

    public function ideation()
    {
        return $this->belongsTo(Ideation::class);
    }
    public function isUserAdmin($userId)
    {
        if(\App\User::find($userId)->is_admin)
            return true;
        else if($this->ideation->owner->id == $userId)
        	return true;

        return false;
    }

    public function reported()
    {
        return $this->morphOne('App\ReportedPost', 'postable');
    }

    public function getIsReportedAttribute()
    {
        $reportedPost = $this->reported;
        if($reportedPost)
            return $reportedPost->reported_by && !$reportedPost->resolved_by;
        return false;
    }

    public function getResolvedByAttribute()
    {
        $reportedPost = $this->reported;
        if($reportedPost)
            return $reportedPost->resolved_by;
        else
            return false;
    }

    public function getTitleAttribute($value)
    {
        return localizedValue('title', $this->localization) ?: $value;
    }

    public function getImageUrlAttribute($value)
    {
        if($value)
            return getS3Url($value);

        return '';
    }
}