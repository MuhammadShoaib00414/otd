<?php

namespace App\Http\Controllers\Admin;

use App\HomePageImage;
use App\Http\Controllers\Controller;
use App\Setting;
use App\User;
use App\VirtualRoom;
use Carbon\Carbon;
use App\Group;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use ariColor;

class OrganizationController extends Controller
{

    public function __construct()
    {
        $this->middleware('otd.team', ['only' => ['instanceSettings', 'updateInstanceSettings']]);
    }

    public function dashboard(Request $request)
    {
        $fromDB = \App\Log::select(DB::raw('count(*) as count, DATE(created_at) as date'))
                             ->groupBy('date')
                             ->where('created_at', '>', Carbon::parse('13 days ago')->toDateTimeString())
                             ->pluck('count', 'date');

        $dates = collect();
        foreach( range( -12, 0 ) AS $i ) {
            $date = Carbon::now()->tz($request->user()->timezone)->addDays( $i )->toDateString();
            $dates->put($date, 0);
        }
        $dates = $dates->merge($fromDB);
        $dates = $dates->sortBy( function($count, $date) {
            return strtotime($date);
        });

        $dates = $dates->mapWithKeys( function($count, $date) {
            return [Carbon::createFromFormat('Y-m-d', $date)->format('M d') => $count];
        });

        $leaderboard = \App\AwardedPoint::with('user')
                                        ->addSelect(DB::raw("SUM(points) as total, user_id"))
                                        ->groupBy('user_id')
                                        ->orderBy('total', 'desc')
                                        ->limit(10)
                                        ->where('created_at', '>', Carbon::parse('-30 days')->toDateTimeString())->get();

        return view('admin.dashboard')->with([
            'activity' => $dates,
            'leaderboard' => $leaderboard,
            'is_pages' => Setting::where('name', 'is_pages')->first(),
        ]);
    }

    public function settings(Request $request)
    {
        $pages = Setting::all();
        $page_service= '';
        foreach($pages as $key=>$value){
            if($value->name = 'pages'){
                $page_service =  $value->value;
            }else{
                $page_service =  'Pages';
            }
           
        }
    
        return view('admin.settings')->with([
            'orgName' => Setting::where('name', '=', 'name')->first(),
            'whitelisted_domains' => Setting::where('name', '=', 'whitelisted_domains')->first(),
            'management_chain' => Setting::where('name', 'is_management_chain_enabled')->first()->value,
            'dashboard_header_image' => Setting::where('name', 'dashboard_header_image')->first(),
            'open_registration' => Setting::where('name', 'open_registration')->first()->value,
            'hide_new_members' => Setting::where('name', 'hide_new_members')->first()->value,
            'group_admins' => Setting::where('name', 'group_admins')->first()->value,
            'homepage_text' => Setting::where('name', 'homepage_text')->first(),
            'account_created_message' => Setting::where('name', 'account_created_message')->first(),
            'logo' => Setting::where('name', 'logo')->first(),
            'dashboard_left_nav_image' => Setting::where('name', 'dashboard_left_nav_image')->first(),
            'dashboard_left_nav_image_link' => Setting::where('name', 'dashboard_left_nav_image_link')->first(),
            'does_dashboard_left_nav_image_open_new_tab' => Setting::where('name', 'does_dashboard_left_nav_image_open_new_tab')->first(),
            'home_page_images' => HomePageImage::where('lang', 'en')->get(),
            'home_page_images_es' => HomePageImage::where('lang', 'es')->get(),
            'primary_color' => Setting::where('name', 'primary_color')->first(),
            'accent_color' => Setting::where('name', 'accent_color')->first(),
            'navbar_color' => Setting::where('name', 'navbar_color')->first(),
            'from_email_name' => Setting::where('name', 'from_email_name')->first(),
            'my_groups_page_name' => Setting::where('name', 'my_groups_page_name')->first(),
            'ask_a_mentor' => Setting::where('name', 'is_ask_a_mentor_enabled')->first()->value,
            'is_ideation_approval_enabled' => Setting::where('name', 'is_ideation_approval_enabled')->first()->value,
            'groups' => Group::whereNull('parent_group_id')->orderBy('name')->get(),
            'is_superpower_enabled' => Setting::where('name', 'is_superpower_enabled')->first()->value,
            'is_about_me_enabled' => Setting::where('name', 'is_about_me_enabled')->first()->value,
            'technical_assistance_email' => Setting::where('name', 'technical_assistance_email')->first()->value,
            'is_technical_assistance_link_enabled' => Setting::where('name', 'is_technical_assistance_link_enabled')->first()->value,
            'is_job_title_enabled' => Setting::where('name', 'is_job_title_enabled')->first()->value,
            'is_company_enabled' => Setting::where('name', 'is_company_enabled')->first()->value,
            'new_message_text' => Setting::where('name', 'new_message_text')->first(),
            'show_join_button_on_group_pages' => Setting::where('name', 'show_join_button_on_group_pages')->first()->value,
            'group_header_color' => Setting::where('name', 'group_header_color')->first()->value,
            'is_ideations_enabled' => Setting::where('name', 'is_ideations_enabled')->first()->value,
            'is_points_enabled' => Setting::where('name', 'is_points_enabled')->first()->value,
            'is_likes_enabled' => Setting::where('name', 'is_likes_enabled')->first()->value,
            'superpower_prompt' => Setting::where('name', 'superpower_prompt')->first(),
            'summary_prompt' => Setting::where('name', 'summary_prompt')->first(),
            'ask_a_mentor_alias' => Setting::where('name', 'ask_a_mentor_alias')->first(),
            'find_your_people_alias' => Setting::where('name', 'find_your_people_alias')->first(),
            'are_group_codes_enabled' => Setting::where('name', 'are_group_codes_enabled')->first()->value,
            'pages' => $page_service

            
        ]);
    }
   
    public function updateSettings(Request $request)
    {
        $checkvalue = Setting::where('name','=','pages')->count();
       if($checkvalue > 0){
        Setting::where('name', '=', 'pages')->update([
            'name' => 'pages',
            'value' => $request->pages,
        ]);
       }else{
            $pageInsert = new Setting;
            $pageInsert->name = 'pages';
            $pageInsert->value = $request->pages;
            $pageInsert->save();
       }
  
        Setting::where('name', '=', 'name')->update([
            'value' => $request->name,
            'localization' => $request->localized_name,
        ]);

        Setting::where('name', 'find_your_people_alias')->update([
            'value' => $request->find_your_people_alias,
        ]);

        Setting::where('name', 'ask_a_mentor_alias')->update([
            'value' => $request->ask_a_mentor_alias,
        ]);

        Setting::where('name', 'are_group_codes_enabled')->update([
            'value' => $request->are_group_codes_enabled,
        ]);

        Setting::where('name', 'superpower_prompt')->update([
            'value' => $request->superpower_prompt,
            'localization' => $request->localized_superpower_prompt,
        ]);
        Setting::where('name', 'summary_prompt')->update([
            'value' => $request->summary_prompt,
            'localization' => $request->localized_summary_prompt,
        ]);
        Setting::where('name', 'is_likes_enabled')->update([
            'value' => $request->is_likes_enabled,
        ]);
        Setting::where('name', 'is_ideations_enabled')->update([
            'value' => $request->is_ideations_enabled,
        ]);
        Setting::where('name', 'group_header_color')->update([
            'value' => $request->group_header_color,
        ]);
        Setting::where('name', '=', 'whitelisted_domains')->update([
            'value' => $request->whitelisted_domains,
        ]);
        Setting::where('name', '=', 'is_management_chain_enabled')->update([
            'value' => $request->management_chain,
        ]);
        Setting::where('name', '=', 'from_email_name')->update([
            'value' => $request->from_email_name,
            'localization' => $request->localized_from_email_name,
        ]);
        Setting::where('name', 'is_technical_assistance_link_enabled')->update([
            'value' => $request->is_technical_assistance_link_enabled,
        ]);
        Setting::where('name', 'technical_assistance_email')->update([
            'value' => $request->technical_assistance_email,
        ]);
        Setting::where('name', 'new_message_text')->update([
            'value' => $request->new_message_text,
            'localization' => $request->has('localization') ? ['es' => ['new_message_text' => $request->localization['es']['new_message_text']]] : json_encode(''),
        ]);
        Setting::where('name', 'show_join_button_on_group_pages')->update([
            'value' => $request->show_join_button_on_group_pages,
        ]);
        Setting::updateOrCreate(['name' => 'group_admins'], ['value' => $request->group_admins]);
        Setting::updateOrCreate(['name' => 'hide_new_members'], ['value' => $request->hide_new_members]);
        Setting::updateOrCreate(['name' => 'open_registration'], ['value' => $request->open_registration]);
        
        Setting::updateOrCreate(['name' => 'homepage_text'], [
            'value' => $request->homepage_text,
            'localization' => $request->has('localization') ? ['es' => ['homepage_text' => $request->localization['es']['homepage_text']]] : '',
        ]);
        Setting::updateOrCreate(['name' => 'account_created_message'], ['value' => $request->account_created_message, 
            'localization' => $request->has('localization') ? ['es' => ['account_created_message' => $request->localization['es']['account_created_message']]] : '',
        ]);
        Setting::where('name', '=', 'primary_color')->update([
            'value' => $request->primary_color,
        ]);
        Setting::where('name', '=', 'accent_color')->update([
            'value' => $request->accent_color,
        ]);
        Setting::where('name', '=', 'navbar_color')->update([
            'value' => $request->navbar_color,
        ]);
        Setting::where('name', 'does_dashboard_left_nav_image_open_new_tab')->update([
            'value' => $request->has('does_dashboard_left_nav_image_open_new_tab') ? 1 : 0,
        ]);
        Setting::where('name', 'dashboard_left_nav_image_link')->update([
            'value' => $request->input('dashboard_left_nav_image_link'),
        ]);
        Setting::where('name', 'my_groups_page_name')->update([
            'value' => $request->input('my_groups_page_name'),
            'localization' => $request->has('localized_my_groups_page_name') ? $request->localized_my_groups_page_name : null,
        ]);
        Setting::where('name', 'is_ideation_approval_enabled')->update([
            'value' => $request->input('is_ideation_approval_enabled'),
        ]);
        Setting::where('name', 'is_points_enabled')->update([
            'value' => $request->input('is_points_enabled'),
        ]);
        Setting::where('name', 'is_superpower_enabled')->update(['value' => $request->is_superpower_enabled]);
        Setting::where('name', 'is_about_me_enabled')->update(['value' => $request->is_about_me_enabled]);
        Setting::where('name', 'is_company_enabled')->update(['value' => $request->is_company_enabled]);
        Setting::where('name', 'is_job_title_enabled')->update(['value' => $request->is_job_title_enabled]);
        Setting::where('name', 'is_ask_a_mentor_enabled')->update(['value' => $request->input('ask_a_mentor')]);
        $this->makeStyles($request->primary_color, $request->accent_color, $request->navbar_color);

        if($request->open_registration && !Setting::where('name', 'registration_key')->count()) {
            Setting::create([
                'name' => 'registration_key',
                'value' => substr(Hash::make(config('app.key')), 0, 6),
            ]);
        }

        if($request->has('stripe_key') && $request->stripe_key != '' && $request->has('stripe_secret') && $request->stripe_secret != '')
        {
            Setting::where('name', 'stripe_key')->update([
                'value' => Crypt::encrypt($request->stripe_key),
            ]);
            Setting::where('name', 'stripe_secret')->update([
                'value' => Crypt::encrypt($request->stripe_secret),
            ]);

            \Illuminate\Support\Env::getRepository()->set('STRIPE_KEY', $request->stripe_key);
            \Illuminate\Support\Env::getRepository()->set('STRIPE_SECRET', $request->stripe_secret);
        }

        if ($request->has('gdpr_prompt'))
            Setting::where('name', 'gdpr_prompt')->update(['value' => $request->gdpr_prompt]);

        if ($request->has('gdpr_checkbox_label'))
            Setting::where('name', 'gdpr_checkbox_label')->update(['value' => $request->gdpr_checkbox_label]);

        if ($request->dashboard_header_type == 'virtual_room') {
            Setting::updateOrCreate(['name' => 'is_dashboard_virtual_room_enabled'], ['value' => 1]);
            $dashboardVirtualRoomId = getSetting('dashboard_virtual_room_id');
            if (!$dashboardVirtualRoomId) {
                $dashboardVirtualRoom = VirtualRoom::create([]);
                Setting::updateOrCreate(['name' => 'dashboard_virtual_room_id'], ['value' => $dashboardVirtualRoom->id]);
            }
            $mobileVirtualRoomId = getSetting('mobile_dashboard_virtual_room_id');
            if (!$mobileVirtualRoomId) {
                $mobileDashboardVirtualRoom = VirtualRoom::create(['is_mobile'=> 1]);
                Setting::updateOrCreate(['name' => 'mobile_dashboard_virtual_room_id'], ['value' => $mobileDashboardVirtualRoom->id]);
            }
        } elseif ($request->dashboard_header_type == 'image')
            Setting::updateOrCreate(['name' => 'is_dashboard_virtual_room_enabled'], ['value' => 0]);

        if($request->has('dashboard_header_image') && !$request->has('dashboard_header_image_revert'))
            Setting::updateOrCreate(['name' => 'dashboard_header_image'], ['value' => $request->dashboard_header_image->store('dashboard-header', 's3')]);
        else if($request->has('dashboard_header_image_revert'))
            Setting::where('name', 'dashboard_header_image')->update(['value' => null]);

        if($request->has('logo_revert'))
            Setting::where('name', 'logo')->update(['value' => '/images/logo-2.png']);
        else if($request->has('logo'))
            Setting::where('name', 'logo')->update(['value' => $request->logo->store('images', 's3')]);
        if($request->has('logo_localization') || $request->has('logo_revert_es'))
        {
            $localization['es']['logo'] = $request->has('logo_revert_es') ? '/images/logo-2.png' : $request->logo_localization['es']['logo']->store('images');
            Setting::where('name', 'logo')->update(['localization' => $localization]);
        }

        if($request->has('dashboard_left_nav_image_remove'))
            Setting::where('name', 'dashboard_left_nav_image')->update(['value' => null]);
        else if($request->has('dashboard_left_nav_image'))
            Setting::updateOrCreate(['name' => 'dashboard_left_nav_image'], ['value' => $request->dashboard_left_nav_image->store('images', 's3')]);
        if($request->has('dashboard_left_nav_image_localization') || $request->has('dashboard_left_nav_image_es_remove'))
        {
            $left_nav_localization['es']['dashboard_left_nav_image'] = $request->has('dashboard_left_nav_image_remove_es') ? null : $request->dashboard_left_nav_image_localization['es']['dashboard_left_nav_image']->store('images', 's3');
            Setting::where('name', 'dashboard_left_nav_image')->update(['localization' => $left_nav_localization]);
        }

        if($request->has('home_page_images'))
        {
            foreach($request->home_page_images as $id => $image)
            {
                HomePageImage::where('id', $id)->update([
                    'image_url' => $image->store('images', 's3')
                ]);
            }
        }
        if($request->has('home_page_image_remove'))
        {
            $toRemove = collect($request->home_page_image_remove)->filter(function($val) {
                return $val == true;
            });
            HomePageImage::whereIn('id', $toRemove->keys())->update(['image_url' => null]);
        }

        if($request->has('home_page_image_localization') || $request->has('home_page_image_revert_es'))
        {
            $home_page_image_localization['es']['home_page_image'] = $request->has('home_page_image_revert_es') ? null : '/uploads/' . $request->home_page_image_localization['es']['home_page_image']->store('images', 's3');
            Setting::where('name', 'home_page_image')->update(['localization' => $home_page_image_localization]);
        }

        Cache::forget('settings');
        Cache::forget('settings-for-api');
        Cache::forget('dashboard-header-for-api');
        Cache::forget('theme-colors');

        return redirect('/admin/settings');
    }

  

    public function billing(Request $request)
    {
        return view('admin.billing');
    }

    protected function makeStyles($primary, $accent, $navbar)
    {
        setThemeColors();
    }

    public function instanceSettings()
    {

        return view('admin.instance')->with([
            'is_management_chain_enabled' => Setting::where('name', 'is_management_chain_enabled')->first(),
            'is_departments_enabled' => Setting::where('name', 'is_departments_enabled')->first(),
            'is_localization_enabled' => Setting::where('name', 'is_localization_enabled')->first(),
            'is_discussion_sms_notifications_enabled' => Setting::where('name', 'is_discussion_sms_notifications_enabled')->first(),
            'is_post_sms_notifications_enabled' => Setting::where('name', 'is_post_sms_notifications_enabled')->first(),
            'is_event_sms_notifications_enabled' => Setting::where('name', 'is_event_sms_notifications_enabled')->first(),
            'is_ideation_sms_notifications_enabled' => Setting::where('name', 'is_ideation_sms_notifications_enabled')->first(),
            'is_introduction_sms_notifications_enabled' => Setting::where('name', 'is_introduction_sms_notifications_enabled')->first(),
            'is_message_sms_notifications_enabled' => Setting::where('name', 'is_message_sms_notifications_enabled')->first(),
            'is_shoutout_sms_notifications_enabled' => Setting::where('name', 'is_shoutout_sms_notifications_enabled')->first(),
            'is_sequence_enabled' => Setting::where('name', 'is_sequence_enabled')->first(),
            'is_stripe_enabled' => Setting::where('name', 'is_stripe_enabled')->first(),
            'is_gdpr_enabled' => Setting::where('name', 'is_gdpr_enabled')->first(),
            'is_pages' => Setting::where('name', 'is_pages')->first(),
        ]);
    }

    public function updateInstanceSettings(Request $request)
    {
        Setting::where('name', 'is_management_chain_enabled')->update([
            'value' => $request->has('is_management_chain_enabled') ? 1 : 0,
        ]);

        Setting::where('name', 'is_departments_enabled')->update([
            'value' => $request->has('is_departments_enabled') ? 1 : 0,
        ]);

        Setting::where('name', 'is_stripe_enabled')->update([
            'value' => $request->has('is_stripe_enabled') ? 1 : 0,
        ]);

        Setting::where('name', 'is_localization_enabled')->update([
            'value' => $request->has('is_localization_enabled') ? 1 : 0,
        ]);

        Setting::where('name', 'is_gdpr_enabled')->update([
            'value' => $request->has('is_gdpr_enabled') ? 1 : 0,
        ]);

        Setting::where('name', 'is_discussion_sms_notifications_enabled')->update([
            'value' => $request->has('is_discussion_sms_notifications_enabled') ? 1 : 0,
        ]);

        Setting::where('name', 'is_post_sms_notifications_enabled')->update([
            'value' => $request->has('is_post_sms_notifications_enabled') ? 1 : 0,
        ]);

        Setting::where('name', 'is_event_sms_notifications_enabled')->update([
            'value' => $request->has('is_event_sms_notifications_enabled') ? 1 : 0,
        ]);

        Setting::where('name', 'is_ideation_sms_notifications_enabled')->update([
            'value' => $request->has('is_ideation_sms_notifications_enabled') ? 1 : 0,
        ]);

        Setting::where('name', 'is_introduction_sms_notifications_enabled')->update([
            'value' => $request->has('is_introduction_sms_notifications_enabled') ? 1 : 0,
        ]);

        Setting::where('name', 'is_message_sms_notifications_enabled')->update([
            'value' => $request->has('is_message_sms_notifications_enabled') ? 1 : 0,
        ]);

        Setting::where('name', 'is_shoutout_sms_notifications_enabled')->update([
            'value' => $request->has('is_shoutout_sms_notifications_enabled') ? 1 : 0,
        ]);

        Setting::where('name', 'is_sequence_enabled')->update([
            'value' => $request->has('is_sequence_enabled') ? 1 : 0,
        ]);

        Setting::where('name', 'is_pages')->update([
            'value' => $request->has('is_pages') ? 1 : 0,
        ]);

        Cache::forget('settings');
      
        return redirect('/admin/instance-settings');
    }

}
