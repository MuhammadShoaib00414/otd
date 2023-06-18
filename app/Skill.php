<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $guarded = [
        'id',
    ];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function ActiveUsers()
    {
    	return $this->users()->where('is_enabled', '=', '1')->whereNotNull('job_title')->where('is_hidden', '=', '0');
    }

    public function getHasUsersAttribute()
    {
        return $this->ActiveUsers()->count() ? true : false;
    }

    public function getTypeAttribute()
    {
        return 'Skillset';
    }
}
