<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdeationPost extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function getFormattedBodyAttribute()
    {
        $body = $this->body;
        $url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
        $body = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $body);
        $body = nl2br($body);

        return $body;
    }

    public function reported()
    {
        return $this->morphOne('App\ReportedPost', 'postable');
    }

    public function ideation()
    {
        return $this->belongsTo(\App\Ideation::class);
    }

    public function getReportedByAttribute()
    {
        $reportedPost = $this->reported;
        if($reportedPost)
            return $reportedPost->reported_by;
        else
            return false;
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

    public function isUserAdmin($userId)
    {
        if(\App\User::find($userId)->is_admin)
            return true;

        return false;
    }

}
