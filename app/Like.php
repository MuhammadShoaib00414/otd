<?php

namespace App;

use App\User;
use App\Post;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    public $fillable = ['user_id', 'postable_id', 'postable_type'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function postable()
    {
        return $this->morphTo();
    }
}
