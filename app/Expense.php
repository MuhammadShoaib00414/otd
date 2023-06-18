<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $guarded = ['id'];

    protected $dates = ['updated_at', 'created_at', 'date'];

    protected $casts = [
        'localization' => 'array',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function getReceiptPathAttribute()
    {
        return getS3Url($this->receipt_file_path);
    }

    public function getDescriptionAttribute($value)
    {
        return localizedValue('description', $this->localization) ?: $value;
    }
}
