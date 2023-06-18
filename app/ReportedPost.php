<?php

namespace App;

use App\Post;
use App\User;
use Illuminate\Database\Eloquent\Model;

class ReportedPost extends Model
{
    protected $guarded = ['id'];
    protected $table = 'reported_posts';

    public function reported_by()
    {
    	return $this->belongsTo(User::class);
    }

    public function getResolvedByUserAttribute()
    {
        if($this->resolved_by)
            return \App\User::find($this->resolved_by);
        return false;
    }

    public function postable()
    {
        return $this->morphTo();
    }
}
