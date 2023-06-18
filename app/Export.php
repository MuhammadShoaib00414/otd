<?php

namespace App;

use App\Mail\ExportCompleted;
use Illuminate\Database\Eloquent\Model;

class Export extends Model
{
    protected $guarded = ['id'];

    protected $dates = ['updated_at', 'created_at'];

    public function send()
    {
        if ($this->send_to)
            \Mail::to($this->send_to)->send(new ExportCompleted($this));
    }

    public function segment()
    {
        return $this->belongsTo(Segment::class);
    }
}
