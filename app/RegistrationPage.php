<?php

namespace App;

use App\Group;
use App\Ticket;
use App\Receipt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrationPage extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    
    protected $casts = [
        'assign_to_groups' => 'array', 
        'addons' => 'array',
        'coupon_codes' => 'array',
        'localization' => 'array',
    ];

    protected $dates = [
        'created_at',
        'event_date',
        'event_end_date',
    ];

    public function getGroupsAttribute()
    {
        return Group::whereIn('id', $this->assign_to_groups)->orderBy('name', 'asc')->get();
    }

    public function scopeSlug($query, $slug)
    {
        return $query->where('slug', $slug)->first();
    }

    public function getImageUrlAttribute($value)
    {
        $value = localizedValue('image_url', $this->localization) ?: $value;
        
        return $value ? getS3Url($value) : $value;
    }

    public function getPromptAttribute($value)
    {
        return localizedValue('prompt', $this->localization) ?: $value;
    }

    public function getDescriptionAttribute($value)
    {
        return localizedValue('description', $this->localization) ?: $value;
    }

    public function getNameAttribute($value)
    {
        return localizedValue('name', $this->localization) ?: $value;
    }

    public function tickets()
    {
        if(is_stripe_enabled())
            return $this->hasMany(Ticket::class);

        return collect([]);
    }

    public function receipts()
    {
        return $this->hasManyThrough(Receipt::class, Ticket::class);
    }

    public function getAddonPrice($addonId)
    {
        foreach($this->addons as $addon)
        {
            if($addon['id'] == $addonId)
                return $addon['price'] / 100;
        }

        return 0;
    }

    public function findAddon($addonId)
    {
        foreach($this->addons as $addon)
        {
            if($addon['id'] == $addonId)
                return $addon;
        }

        return false;
    }

    public function getTotalWithCouponCode($code, $subtotal)
    {
        if(!$this->coupon_codes)
            return $subtotal;

        $matched_code = false;

        foreach($this->coupon_codes as $coupon_code)
        {
            if($coupon_code['code'] == $code)
                $matched_code = $coupon_code;
        }

        if(!$matched_code)
            return $subtotal;

        $amount = $matched_code['amount'] / 100;
        $type = $matched_code['type'];

        if($type == 'percent')
            return $subtotal * (1 - ($amount / 100));
        else
            return $subtotal - $amount;
    }

    public function getCouponInfo($userEnteredCode)
    {
        $matched_code = false;

        foreach($this->coupon_codes as $coupon_code)
        {
            if($coupon_code['code'] == $userEnteredCode)
            {
                $matched_code = true;
                $code = $coupon_code;
                break;
            }
        }

        if(!isset($code))
            return response()->json(false);

        if($code['type'] == 'fixed')
        {
            $label = " - $" . $code['amount'] / 100;
            $msg = $label . ' off';
        }
        else if($code['type'] == 'percent')
        {
            $label = ' - ' . $code['amount'] / 100 . '%';
            $msg = $label . ' off';
        }
        else
        {
            $msg = false;
            $label = false;
        }

        $response = [
            'type' => $code['type'],
            'amount' => $code['amount'] / 100,
            'message' => $msg,
            'label' => $label,
        ];

        return $response;
    }
}
