<?php

namespace App;

use App\Group;
use App\RegistrationPage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];

    protected $casts = ['add_to_groups' => 'array'];

    public function registrationPage()
    {
        return $this->belongsTo(RegistrationPage::class, 'registration_page_id');
    }

    public function getGroupsAttribute()
    {
        if(!$this->add_to_groups)
            return collect([]);

        return Group::whereIn('id', $this->add_to_groups)->get();
    }

    public function getPriceAttribute($value)
    {
        return (float) parse_money($value / 100);
    }

    public function getDisplayPriceAttribute()
    {
        return display_money($this->price);
    }
}
