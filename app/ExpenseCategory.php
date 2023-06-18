<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'localization' => 'array',
    ];

    public function getNameAttribute($value)
    {
        return localizedValue('name', $this->localization) ?: $value;
    }
}
