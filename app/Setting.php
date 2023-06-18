<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['name', 'value', 'localization'];

    protected $guarded = ['id'];

    protected $casts = ['localization' => 'array'];

    public function getValueAttribute($value)
    {
        $value = localizedValue($this->name, $this->localization) ?: $value;

        if(!$this->is_file || $value == "")
            return $value;

        return getS3Url($value);
    }

    public function value($locale)
    {
    	$value = localizedValue($this->name, $this->localization, $locale) ?: $this->getRawOriginal('value');

        if(!$this->is_file || $value == "")
            return $value;

        return getS3Url($value);
    }
}
