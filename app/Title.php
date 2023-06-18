<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'localization' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getNameAttribute($value)
    {
        return localizedValue('name', $this->localization) ?: $value;
    }
}
