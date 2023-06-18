<?php

namespace App\Http\Controllers\Admin\System;

use App\Category;
use App\Group;
use App\Http\Controllers\Controller;
use App\Question;
use App\Setting;
use App\Taxonomy;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class OnboardingSettings extends Controller
{

    public function index()
    {
        \Cache::clear();
        return view('admin.onboarding.steps')->with([
            'settings' => json_decode(getsetting('onboarding_settings'), true),
        ]);
    }

    public function editWelcome()
    {
        return view('admin.onboarding.steps.welcome')->with([
            'settings' => json_decode(getsetting('onboarding_settings'), true),
        ]);
    }

    public function storeWelcome(Request $request)
    {
        $settings = json_decode(getsetting('onboarding_settings'), true);
        $settings['intro'] = $request->intro;
        $settings['intro']['active'] = $request->has('intro.active');
        Setting::where('name', 'onboarding_settings')->update([
            'value' => $settings,
        ]);

        \Cache::clear();

        return redirect()->back()->with('success', 'Saved!');
    }

    public function editIntroVideo()
    {
        return view('admin.onboarding.steps.introvideo')->with([
            'settings' => json_decode(getsetting('onboarding_settings'), true),
        ]);
    }

    public function storeIntroVideo(Request $request)
    {
        $settings = json_decode(getsetting('onboarding_settings'), true);
        $settings['embed_video'] = $request->embed_video;
        $settings['embed_video_active'] = $request->has('embed_video_active');
        Setting::where('name', 'onboarding_settings')->update([
            'value' => $settings,
        ]);

        \Cache::clear();

        return redirect()->back()->with('success', 'Saved!');
    }

    public function editBasic()
    {
     

        $settingOnboarding = [
            'is_name_lable' =>  getSetting('is_name_lable'),
            'is_gender_required' => getSetting('is_gender_required'),
            'is_gender' =>  getSetting('is_gender'),
            'is_location_required' =>  getSetting('is_location_required'),
            'is_location' =>  getSetting('is_location'),
        ];

        return view('admin.onboarding.steps.basic')->with([
            'settings' => json_decode(getsetting('onboarding_settings'), true),
            'setting' => $settingOnboarding
        ]);
    }

    public function storeBasic(Request $request)
    {

        $is_name_lable = $request->is_name_lable;
        $is_location = $request->is_location;
        $is_location_required = $request->is_location_required;
        $is_gender = $request->is_gender;


        $settings = json_decode(getsetting('onboarding_settings'), true);

        $settings['basic'] = $request->basic;
        $settings['basic']['active'] = $request->has('basic.active');

        Setting::where('name', 'onboarding_settings')->update([
            'value' => $settings,
        ]);
        Setting::where('name', 'is_name_lable')->update([
            'value' => $request->is_name_lable,
        ]);

        Setting::where('name', 'is_location')->update([
            'value' => $is_location,
        ]);
        Setting::where('name', 'is_location_required')->update([
            'value' =>  $is_location_required,
        ]);

        Setting::where('name', 'is_gender')->update([
            'value' => $is_gender,
        ]);
        \Cache::clear();

        return redirect()->back()->with('success', 'Saved!');
    }

    public function editImageBio()
    {
      
        return view('admin.onboarding.steps.image-bio')->with([
            'settings' => json_decode(getsetting('onboarding_settings'), true),
        
        ]);
    }

    public function storeImageBio(Request $request)
    {
        $settings = json_decode(getsetting('onboarding_settings'), true);
        $settings['imagebio'] = $request->imagebio;
        $settings['imagebio']['active'] = $request->has('imagebio.active');
        Setting::where('name', 'onboarding_settings')->update([
            'value' => $settings,
        ]);

        \Cache::clear();

        return redirect()->back()->with('success', 'Saved!');
    }

    public function editAboutMe()
    {
        return view('admin.onboarding.steps.about-me')->with([
            'settings' => json_decode(getsetting('onboarding_settings'), true),
        ]);
    }

    public function storeAboutMe(Request $request)
    {
        $settings = json_decode(getsetting('onboarding_settings'), true);
        $settings['about'] = $request->about;
        $settings['about']['active'] = $request->has('about.active');
        Setting::where('name', 'onboarding_settings')->update([
            'value' => $settings,
        ]);

        \Cache::clear();

        return redirect()->back()->with('success', 'Saved!');
    }

    public function editCategory(Taxonomy $taxonomy)
    {
        return view('admin.onboarding.steps.category')->with([
            'taxonomy' => $taxonomy,
        ]);
    }

    public function storeCategory(Taxonomy $taxonomy)
    {
    }

    public function editQuestions()
    {
        return view('admin.onboarding.steps.questions')->with([
            'settings' => json_decode(getsetting('onboarding_settings'), true),
        ]);
    }

    public function storeQuestions(Request $request)
    {
        $settings = json_decode(getsetting('onboarding_settings'), true);
        $settings['questions'] = $request->questions;
        $settings['questions']['active'] = $request->has('questions.active');
        Setting::where('name', 'onboarding_settings')->update([
            'value' => $settings,
        ]);

        \Cache::clear();

        return redirect()->back()->with('success', 'Saved!');
    }

    public function editNotifications()
    {
        return view('admin.onboarding.steps.notifications')->with([
            'settings' => json_decode(getsetting('onboarding_settings'), true),
        ]);
    }

    public function storeNotifications(Request $request)
    {
        $settings = json_decode(getsetting('onboarding_settings'), true);
        $settings['notifications'] = $request->completed;
        $settings['notifications']['active'] = $request->has('notifications.active');
        Setting::where('name', 'onboarding_settings')->update([
            'value' => $settings,
        ]);

        \Cache::clear();

        return redirect()->back()->with('success', 'Saved!');
    }

    public function editGroupsSelection()
    {
        return view('admin.onboarding.steps.groups')->with([
            'settings' => json_decode(getsetting('onboarding_settings'), true),
        ]);
    }

    public function storeGroupsSelection(Request $request)
    {
        $settings = json_decode(getsetting('onboarding_settings'), true);
        $settings['groups'] = $request->completed;
        $settings['groups']['active'] = $request->has('groups.active');
        Setting::where('name', 'onboarding_settings')->update([
            'value' => $settings,
        ]);

        \Cache::clear();

        return redirect()->back()->with('success', 'Saved!');
    }

    public function editGdpr()
    {
        return view('admin.onboarding.steps.gdpr');
    }

    public function storeGdpr(Request $request)
    {
        Setting::where('name', 'gdpr_prompt')->update(['value' => $request->gdpr_prompt]);
        Setting::where('name', 'gdpr_checkbox_label')->update(['value' => $request->gdpr_checkbox_label]);
        \Cache::clear();

        return redirect()->back()->with('success', 'Saved!');
    }

    public function editCompleted()
    {
        \Cache::clear();
        return view('admin.onboarding.steps.completed')->with([
            'settings' => json_decode(getsetting('onboarding_settings'), true),
        ]);
    }

    public function storeCompleted(Request $request)
    {
        $settings = json_decode(getsetting('onboarding_settings'), true);
        $settings['completed'] = $request->completed;
        $settings['completed']['active'] = $request->has('completed.active');
        Setting::where('name', 'onboarding_settings')->update([
            'value' => $settings,
        ]);

        \Cache::clear();

        return redirect()->back()->with('success', 'Saved!');
    }

    public function stepPreview(Request $request)
    {
        $frequencyOptions = [
            'immediately',
            'daily',
        ];
        $groups = Group::where(function ($query) {
            $query->whereNull('parent_group_id')->where('is_private', 0);
        })->orWhere(function ($query) use ($request) {
            $query->whereNull('parent_group_id')->whereIn('id', $request->user()->groups()->pluck('id'));
        })->orderBy('name', 'asc')->get();

        if ($request->user()->groups()->count())
            $groups = $groups->merge($request->user()->groups()->whereNull('parent_group_id')->get());
        if ($groups->count() == 1) {
            $request->user()->groups()->syncWithoutDetaching($groups);
        }

        if ($request->user()->is_event_only == 1)
            $groups = collect([]);

        $questions = Question::enabled()->topLevel()->where('locale', $request->user()->locale)->orderBy('order_key', 'asc')->get();

        $onboarding_settings = json_decode(getsetting('onboarding_settings'), true);
        $frequencyOptions = [
            'immediately',
            'daily',
        ];
        $agent = new Agent();
        $deviceType = $agent->device() . ', ' . $agent->browser();
        $deviceName = $agent->platform();
        $isRegistered = $request->user()->devices()
            ->where('device_name', $agent->device() . ', ' . $agent->browser())
            ->where('device_type', $agent->platform())->count();

        
        $settingOnboarding = [
            'is_name_lable' =>  getSetting('is_name_lable'),
            'is_gender_required' => getSetting('is_gender_required'),
            'is_gender' =>  getSetting('is_gender'),
            'is_location_required' =>  getSetting('is_location_required'),
            'is_location' =>  getSetting('is_location'),
        ];
           
        return view('onboarding.adminpreview')->with([
            'step' => $request->step,
            'groups' => $groups,
            'questions' => $questions,
            'frequencyOptions' => $frequencyOptions,
            'settings' => $onboarding_settings,
            'isRegistered' => $isRegistered,
            'user' => $request->user(),
            'frequencyOptions' => $frequencyOptions,
            'setting' => $settingOnboarding
        ]);
    }
}
