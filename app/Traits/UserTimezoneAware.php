<?php

namespace App\Traits;

use Auth;
use DateTime;
use datetimezone;
use Carbon\Carbon;

trait UserTimezoneAware
{
    
    /**
     * The attribute name containing the timezone (defaults to "timezone").
     *
     * @var string
     */
    public $timezoneAttribute = 'timezone';
    
    /**
     * Return the passed date in the user's timezone (or default to the app timezone)
     *
     * @return string
     */
    public function getDateToUserTimezone($date, $timezone = null)
    {
        if ($timezone == null) {
            if (Auth::check()) {
                $timezone = Auth::user()->timezone;
            } else {
                $timezone = Config::get('app.timezone');
            }
        }
        $datetime = new DateTime($date);
        $datetime->setTimezone(new datetimezone($timezone));
        return new Carbon($datetime->format('c'));
    }

}