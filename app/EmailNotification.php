<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailNotification extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
    	'localization' => 'array',
    ];

    public function subjectWithLocale($locale)
    {
    	if($locale == '' || $locale == null || $locale == 'en')
    		return $this->subject;
    	
    	if(isset($this->localization[$locale]['subject']))
    		return $this->localization[$locale]['subject'];

    	return $this->subject;
    }

    public function templateWithLocale($locale)
    {
        if($locale == '' || $locale == null || $locale == 'en' || !getsetting('is_localization_enabled'))
            return $this->email_template;
        
        if(isset($this->localization[$locale]['email_template']))
            return $this->localization[$locale]['email_template'];

        return $this->email_template;
    }

    public function htmlWithLocale($locale)
    {
        if($locale == '' || $locale == null || $locale == 'en' || !getsetting('is_localization_enabled'))
            return $this->email_html;
        
        if(isset($this->localization[$locale]['email_html']))
            return $this->localization[$locale]['email_html'];

        return $this->email_html;
    }
}
