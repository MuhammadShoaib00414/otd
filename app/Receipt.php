<?php

namespace App;

use App\User;
use App\Ticket;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['details' => 'array', 'access_granted' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function getAmountPaidAttribute($value)
    {
        return (float) $value / 100;
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
