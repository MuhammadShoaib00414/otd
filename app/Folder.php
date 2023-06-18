<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'localization' => 'array',
    ];

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function getNameAttribute($value)
    {
        return localizedValue('name', $this->localization) ?: $value;
    }
}
