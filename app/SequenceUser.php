<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SequenceUser extends Pivot
{
    public $incrementing = true;
}
