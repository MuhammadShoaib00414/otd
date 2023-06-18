<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
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

    public function getTypeAttribute()
    {
        return 'Hustle';
    }

    public function getHasUsersAttribute()
    {
        return $this->ActiveUsers()->count() ? true : false;
    }

    public function scopeOrderByUsers($query, $users)
    {
        return $query->with('users')
                    ->withCount('users')
                    ->orderBy('users_count', 'desc')
                    ->whereHas('users', function($query) use($users) {
                        return $query->where('users.id', 'in', $users);
                    });
    }
}
