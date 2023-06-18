<?php

namespace App;

use App\Question;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $guarded = [
        'id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'options' => 'json',
        'localization' => 'array',
    ];

    protected static function booted()
    {
        static::deleting(function ($question) {
            $question->children->each(function ($childQuestion) {
                $childQuestion->delete();
            });
        });
    }

    public function children()
    {
        return $this->hasMany(Question::class, 'parent_question_id');
    }
   
    public function parent()
    {
        return $this->belongsTo(Question::class, 'parent_question_id');
    }

    public function scopeEnabled()
    {
        return $this->where('is_enabled', 1);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_question_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function getPromptAttribute($value)
    {
        return localizedValue('prompt', $this->localization) ?: $value;
    }
}
