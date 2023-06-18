<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TitleUser extends Pivot
{
    public function assigned()
    {
        return $this->belongsTo(User::class, 'assigned_id');
    }
}