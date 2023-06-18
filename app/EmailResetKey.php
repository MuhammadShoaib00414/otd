<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EmailResetKey extends Model
{
    protected $guarded = ['id'];

    protected $dates = ['verified_at'];

    protected $table = 'email_resets';

    public function scopeFindKey($query, $key)
    {
    	return $query->where('key', $key)->first();
    }

    public function scopeKeyExists($query, $key)
    {
        return $query->where('key', $key)->exists();
    }

    public function verify()
    {
    	$this->update([
    		'verified_at' => Carbon::now(),
    	]);
    }
}
