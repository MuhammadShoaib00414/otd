<?php

namespace App;

use DateTime;
use datetimezone;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function getTimezoneAdjustedDateAttribute()
    {
        $datetime = new DateTime($this->created_at);
        $datetime->setTimezone(new datetimezone($this->user->timezone));
        return new Carbon($datetime->format('c'));
    }

    public function relatedModel()
    {
        return $this->morphTo('related_model');
    }

    public function relatedModelNoMatterWhat()
    {
        return $this->morphTo('related_model')->withTrashed();
    }

    public function secondaryRelatedModel()
    {
        return $this->morphTo('secondary_related_model');
    }
    
    public function getTrackInfoAttribute($value)
    {
        return json_decode($value,true);
    }
}
