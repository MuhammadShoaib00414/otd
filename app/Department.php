<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['id'];

    protected $casts = [
        'localization' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class)->withTrashed();
    }

    public function getNameAttribute($value)
    {
        return localizedValue('name', $this->localization) ?: $value;
    }
}
