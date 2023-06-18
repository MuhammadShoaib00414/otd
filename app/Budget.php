<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $guarded = ['id'];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function getSpentAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    public function getRemainingAttribute()
    {
        return $this->total_budget - $this->spent;
    }
}
