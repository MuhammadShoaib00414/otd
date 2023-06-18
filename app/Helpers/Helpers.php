<?php

use App\Group;
use Illuminate\Http\Request;


function parse_money(string $money) : float {
    if(strpos($money, ',') !== false)
    {
        $money = implode('', explode(',', $money));
    }
    if(strpos($money, '$') !== false)
    {
        $money = implode('', explode('$', $money));
    }

    return (float) round($money, 2);
}

function display_money(float $money)
{
    if(floor($money) == $money)
        return '$' . number_format((int) $money, 0, '', ',');
    return '$' . number_format((float) $money, 2, '.', ',');
}


function getShareableGroups($postType)
{
    // \DB::enableQueryLog();
    $query = \Auth::user()->groups;
      
    if ($postType == 'App\Discussion') {
        $query->where('is_discussions_enabled','1')->where('can_users_post_text','1');
    } else if($postType == 'App\Shoutout') {
        $query->where('is_shoutouts_enabled','1')->where('can_users_post_text','1');
    } else if($postType == 'App\TextPost'){
        $query->where('is_posts_enabled','=','1')->where('can_users_post_text','1');
    }

   return  $query->whereNull('parent_group_id')->sortByDesc('name');
     
    
    // $query->whereNull('parent_group_id')->orderBy('name', 'asc')->get();
}

function getS3Url($path) {
    // this is kept but commented out because it can avoid errors
    // but it slows down load times significantly
    // if(!Storage::disk('s3')->exists($path))
    //     return '';
    if($path == '' || $path == '/' || $path == '//')
        return $path;
    $path = str_replace('//', '/', $path);
    if ($path[0] == '/') {
        $path = substr($path, 1);
    }
    return \Storage::disk('s3')->url($path);
    
    // return Storage::temporaryUrl(
    //     $path,
    //     now()->addMinutes(60)
    // );
 }

 function splitName($name) {
    $name = trim($name);
    $first_name = strtok($name, ' ');
    $last_name = strstr($name, ' ');
    return array($first_name, $last_name);
}

 function getS3DownloadHeaders($path, $name)
{
    $mimeType = Storage::disk('s3')->mimeType($path);

    return [
        'Content-Type'        => $mimeType,
        'Content-Disposition' => 'attachment; filename="'. $name .'"',
    ];
}

function localizedValue($attribute, $localizationArray, $locale = false) {
    if(resolve('settings')->where('name', '=', 'is_localization_enabled')->first()) {
        if(!$locale)
            $locale = \Illuminate\Support\Facades\App::getLocale();
        if(isset($localizationArray[$locale])) {
            if(isset($localizationArray[$locale][$attribute])) {
                return $localizationArray[$locale][$attribute];
            }
        }
    }
    return false;
}

function send_sms($phone, $message)
{
    return;
    $sid    = config('otd.plivo_sid');
    $token  = config('otd.plivo_token');
    $client = new \Plivo\Resources\PHLO\PhloRestClient( $sid, $token );
    $phlo = $client->phlo->get("9ced1863-39ef-425f-9540-29508da358a7");
    try {
        $response = $phlo->run([
            'From' => config('otd.plivo_from'), 
            'To' => $phone,
            'Body' => $message,
        ]);
    } catch (\Plivo\Exceptions\PlivoRestException $ex) {
        return false;
    }
}

function getSetting($name, $locale = 'en')
{
    try {
        if (resolve('settings')->where('name', '=', $name)->first())
            return resolve('settings')->where('name', '=', $name)->first()->value($locale);
        else
            throw new \Exception('');
    } catch (\Exception $e) {
        Cache::forget('settings');
        return \App\Setting::all()->where('name', '=', $name)->first()->value($locale);
    }
}

function optimizeImage($path, $maxWidth)
{
    \Spatie\ImageOptimizer\OptimizerChainFactory::create()->optimize($path);
    try
    {
        $image = Image::make($path);
    }
    catch(Exception $e)
    {
        return $path;
    }
    if($image->width() > $maxWidth)
        $image->resize($maxWidth, null, function($constraint) {
            $constraint->aspectRatio();
        })->save($path);
    return $path;
}

function companySizes()
{
    return  [
        '0 - 10 people',
        '11 - 50 people',
        '51 - 500 people',
        '501 – 5,000 people',
        'Over 5,000 people',
    ];
}

function positions()
{
    return [
        "Sole Proprietor",
        "Employee",
        "First-line Manager",
        "Senior Manager",
        "Director",
        "Vice President",
        "C-Level",
        "Owner",
    ];
}

function education()
{
    return [
        "H.S. Graduate",
        "Some College Courses",
        "Associate Degree",
        "Bachelors Degree",
        "Masters Degree",
        "Doctoral Degree",
    ];
}

function createThemeColors() {
    return \Cache::remember('theme-colors', 7200, function() {
        $primary = \App\Setting::where('name', 'primary_color')->first()->value;
        $accent = \App\Setting::where('name', 'accent_color')->first()->value;
        $navbar = \App\Setting::where('name', 'navbar_color')->first()->value;

        $primaryColor = ariColor::newColor($primary, 'hex');
        $accent = ariColor::newColor($accent, 'hex');
        $navbarColor = ariColor::newColor($navbar, 'hex');

        if ($primaryColor->luminance > 140)
            $primaryColor = $primaryColor->getNew('lightness', $primaryColor->lightness * .1);

        $colors = (Object) [
            'primary' =>[
                '900' => $primaryColor->getNew('lightness', $primaryColor->lightness - $primaryColor->lightness * .7)->toCSS('hex'),
                '800' => $primaryColor->getNew('lightness', $primaryColor->lightness - $primaryColor->lightness * .45)->toCSS('hex'),
                '700' => $primaryColor->getNew('lightness', $primaryColor->lightness - $primaryColor->lightness * .2)->toCSS('hex'),
                '600' => $primaryColor->toCSS('hex'),
                '500' => $primaryColor->getNew('lightness', $primaryColor->lightness + (100 - $primaryColor->lightness) * .2)->toCSS('hex'),
                '400' => $primaryColor->getNew('lightness', $primaryColor->lightness + (100 - $primaryColor->lightness) * .4)->toCSS('hex'),
                '300' => $primaryColor->getNew('lightness', $primaryColor->lightness + (100 - $primaryColor->lightness) * .6)->toCSS('hex'),
                '200' => $primaryColor->getNew('lightness', $primaryColor->lightness + (100 - $primaryColor->lightness) * .75)->toCSS('hex'),
                '100' => $primaryColor->getNew('lightness', $primaryColor->lightness + (100 - $primaryColor->lightness) * .8)->toCSS('hex'),
            ],
            'accent' => [
                '900' => $accent->getNew('lightness', $accent->lightness - $accent->lightness * .7)->toCSS('hex'),
                '800' => $accent->getNew('lightness', $accent->lightness - $accent->lightness * .45)->toCSS('hex'),
                '700' => $accent->getNew('lightness', $accent->lightness - $accent->lightness * .2)->toCSS('hex'),
                '600' => $accent->toCSS('hex'),
                '500' => $accent->getNew('lightness', $accent->lightness + (100 - $accent->lightness) * .2)->toCSS('hex'),
                '400' => $accent->getNew('lightness', $accent->lightness + (100 - $accent->lightness) * .4)->toCSS('hex'),
                '300' => $accent->getNew('lightness', $accent->lightness + (100 - $accent->lightness) * .6)->toCSS('hex'),
                '200' => $accent->getNew('lightness', $accent->lightness + (100 - $accent->lightness) * .75)->toCSS('hex'),
                '100' => $accent->getNew('lightness', $accent->lightness + (100 - $accent->lightness) * .8)->toCSS('hex'),
            ],
            'background' => $primaryColor->getNew('lightness', 98)->toCSS('hex'),
            'navbar_bg' => $navbarColor->toCSS('hex'),
            'navbar_text' => ( $navbarColor->luminance > 140 ) ? '#222222' : '#FFFFFF',
        ];

        return $colors;
    });
}

function setThemeColors()
{
    Cache::put('theme-colors', createThemeColors());
}

function getThemeColors()
{
    return cache('theme-colors', createThemeColors());
}

function linkify($value, $protocols = array('http', 'mail'), array $attributes = array())
{
    // Link attributes
    $attr = '';
    foreach ($attributes as $key => $val) {
        $attr .= ' ' . $key . '="' . htmlentities($val) . '"';
    }
    
    $links = array();
    
    // Extract existing links and tags
    $value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) { return '<' . array_push($links, $match[1]) . '>'; }, $value);
    
    // Extract text links for each protocol
    foreach ((array)$protocols as $protocol) {
        switch ($protocol) {
            case 'http':
            case 'https':   $value = preg_replace_callback('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) { if ($match[1]) $protocol = $match[1]; $link = $match[2] ?: $match[3]; return '<' . array_push($links, "<a $attr href=\"$protocol://$link\">$link</a>") . '>'; }, $value); break;
            case 'mail':    $value = preg_replace_callback('~([^\s<]+?@[^\s<]+?\.[^\s<]+)(?<![\.,:])~', function ($match) use (&$links, $attr) { return '<' . array_push($links, "<a $attr href=\"mailto:{$match[1]}\">{$match[1]}</a>") . '>'; }, $value); break;
            case 'twitter': $value = preg_replace_callback('~(?<!\w)[@#](\w++)~', function ($match) use (&$links, $attr) { return '<' . array_push($links, "<a $attr href=\"https://twitter.com/" . ($match[0][0] == '@' ? '' : 'search/%23') . $match[1]  . "\">{$match[0]}</a>") . '>'; }, $value); break;
            default:        $value = preg_replace_callback('~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) { return '<' . array_push($links, "<a $attr href=\"$protocol://{$match[1]}\">{$match[1]}</a>") . '>'; }, $value); break;
        }
    }
    
    // Insert all link
    return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) { return $links[$match[1] - 1]; }, $value);
}

function flattenGroupTrees($groups)
{
    $output = collect([]);
    foreach($groups as $group) {
        $output = $output->merge(flattenGroupTrees($group->publishable_subgroups_recursive));
    }
    $output = $output->merge($groups);

    return $output;
}

function array_unflatten($array, $defaultKey)
{
    $output = array();

    foreach ($array as $key => $value)
    {
        if(!is_array($value))
            $output[$defaultKey][] = $value;
        else
            $output[$key] = $value;
    }

    return $output;
}

function singular($string)
{
    if(strtolower($string) == 'pet peeves')
        return 'pet peeve';
    else
        return Illuminate\Support\Str::singular($string);
}

function strlimit($string, $maxLength)
{
    if(strlen($string) > $maxLength)
        return substr($string, 0, $maxLength) . '...';
    return $string;
}

function getRemoteMimeType($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);

    # get the content type
    return curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
}

function is_zoom_enabled()
{
    if(!getsetting('origin_trial_key'))
        return false;
    if(!config('otd.plivo_sid'))
        return false;
    if(!config('otd.zoom_api_secret'))
        return false;

    return true;
}

function get_non_consolidatable_notification_types()
{
    return [
        'App\MessageThread',
        'App\Introduction',
        'App\Shoutout',
    ];
}

function errorView($message)
{
    return view('errors.deleted')->with(['message' => $message]);
}

function randomString($length)
{
    return \Illuminate\Support\Str::random($length);
}

function sendPushNotification($device_tokens, $title, $body, $badge = 1, $isAndroid = false)
{
    $url = 'https://fcm.googleapis.com/fcm/send';
    $serverKey = config('app.firebase_key');

    if(!is_array($device_tokens))
        $device_tokens = [$device_tokens];

    $data = [
        "registration_ids" => $device_tokens,
        "notification" => [
            "title" => $title,
            "body" => $body,  
            'url_link' => config('app.url') . '/notifications',
            'badge' => $badge,
        ],
        "webpush" => [
            "headers" => [
                "Urgency" => "high"
            ],
            "notification" => [
                "title" => $title,
                "body" => $body,
                "requireInteraction" => "true",
                "badge" => $badge
            ]
        ],
    ];
    //android is based off of "data", not "notification"
    if($isAndroid)
        $data['data'] = $data['notification'];
    $encodedData = json_encode($data);

    $headers = [
        'Authorization:key=' . $serverKey,
        'Content-Type: application/json',
    ];

    $ch = curl_init();
  
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
    curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

    // Execute post
    $result = curl_exec($ch);

    if ($result === FALSE) {
        \Log::error('Curl failed: ' . curl_error($ch));
        return curl_error($ch);
    }        

    // Close connection
    curl_close($ch);
    \Log::info('Push notification sent: ');
    \Log::info($result);
    return "Notification sent.";
}

function get_stripe_credentials($type = false)
{
    $encrypted_key = getSetting('stripe_key');
    $encrypted_secret = getSetting('stripe_secret');

    $key = $encrypted_key ? \Illuminate\Support\Facades\Crypt::decrypt($encrypted_key) : '';
    $secret = $encrypted_secret ? \Illuminate\Support\Facades\Crypt::decrypt($encrypted_secret) : '';

    if($type)
    {
        if($type == 'key')
            return $key;
        if($type == 'secret')
            return $secret;
    }

    return [
        'key' => $key,
        'secret' => $secret,
    ];
}

function is_stripe_enabled()
{
    return (getsetting('stripe_key') && getsetting('stripe_secret') && getsetting('is_stripe_enabled'));
}


if(!function_exists('get_blocked_users_ids'))
{
    function get_blocked_users_ids()
    {
        $blockedUsers = \App\ReportedUsers::where([
            'reported_by' => auth()->user()->id,
            'status' => 'blocked',
        ])->pluck('user_id');
        $whoBlockedMe = \App\ReportedUsers::where([
            'user_id' => auth()->user()->id,
            'status' => 'blocked',
        ])->pluck('reported_by');
        $blockedUsers = $blockedUsers->merge($whoBlockedMe);
        return $blockedUsers;
    }
}

    function UserTrackInfo()
    {
        $userIp =  \Request::ip();

        $locationData = Location::get($userIp)->toArray();

        $browser = Agent::browser();
        $version = Agent::version($browser);
        $device = Agent::device();
        $platform = Agent::platform();
        if (Agent::isMobile()) {
            $result = 'Yes, This is Mobile.';
        }else if (Agent::isDesktop()) {
            $result = 'Yes, This is Desktop.';
        }else if (Agent::isTablet()) {
            $result = 'Yes, This is Desktop.';
        }else if (Agent::isPhone()) {
            $result = 'Yes, This is Phone.';
        }

        $data = array(
            "ip" =>$userIp,
            "location" => $locationData['countryName'],
            "browser" => $browser,
            "device" =>  $device,
            "CityName" => $locationData['cityName'],
            "version" => $version,
            "platform" =>$platform,
            "Machine" =>$result
        );
       return json_encode($data,true);
    }


if(!function_exists('getLatestMessage'))
{
    function getLatestMessage($message)
    {
        // check if message has tags
        if (!preg_match('/<[^>]*>/', $message->message)) {
            return str_limit($message->message, 55, '...');
        } else {
            return "<i class='fa fa-paperclip'></i> Attachment";
        }
        
    }
}


