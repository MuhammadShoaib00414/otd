<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IdeationSurvey extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'localization' => 'array',
    ];

    public function ideation()
    {
        return $this->belongsTo(Ideation::class);
    }

    public function getTitleAttribute($value)
    {
        return localizedValue('title', $this->localization) ?: $value;
    }
}