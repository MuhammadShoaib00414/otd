<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Group;
use App\Ticket;
use App\Receipt;
use App\MessageThread;
use App\RegistrationPage;
use App\Events\MessageSent;
use App\Events\UserSignedin;
use Illuminate\Http\Request;
use Psy\Readline\Hoa\Exception;
use App\Events\PaymentProcessed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['addToCalendar', 'getPrice', 'registerUser', 'register', 'checkCoupon']);
        $this->middleware('locale')->only('openSignup');
        $this->middleware('stripe');
        Cache::flush();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
    }

    public function pickRegistration()
    { 
        return view('auth.pick-register')->with([
            'pages' => RegistrationPage::where('is_welcome_page_accessible', 1)->withTrashed()->orderBy('name', 'desc')->get(),
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_hidden' => getsetting('hide_new_members'),
        ]);
    }

    public function register($slug)
    {
        $regPage = RegistrationPage::slug($slug)->withTrashed()->first();
       
        // $pages = RegistrationPage::slug($slug)->withTrashed()->first();
 
        if(empty($regPage))
            return redirect('/');

        $totaladdons = (isset($regPage->addons)) ? count($regPage->addons) : 0;
        return view('auth.customregister')->with([
  
            'page' => $regPage,
             'totaladdons' => $totaladdons,
             'ticket'   => Ticket::get()
        ]);

        
        
    }

    public function registerUser($slug, Request $request)
    {
      
        try {
            DB::beginTransaction();
            $registrationPage = RegistrationPage::slug($slug)->withTrashed()->first();
           
            $this->validate($request, [
                'g-recaptcha-response' => 'required|captcha',
                ]);
            $hasAddons = (is_array($registrationPage->addons) && count($registrationPage->addons) > 0);
            if (!$request->has('ticket') && !$request->has('addons')) {
              if (!$request->user() && $registrationPage->tickets()->count() > 0) {
                  return redirect()->back()->withErrors(['Please select a ticket']);
              } else if (!$request->user() && $registrationPage->tickets()->count() == 0 && $hasAddons) {
                  return redirect()->back()->withErrors(['Please select an addon']);
              } else if ($request->user() && $registrationPage->tickets()->count() > 0 && !$request->user()->hasBoughtTicketForRegistrationPage($registrationPage->id)) {
                  return redirect()->back()->withErrors(['You must purchase a ticket to register for this event.']);
              } 
            } else if ($registrationPage->tickets()->count() == 0 && !$request->has('addons')) {    
                return redirect()->back()->withErrors(['Please select an addon']);
            }
          
            $ticket = Ticket::find($request->ticket);
            if($ticket)
                $subtotal = $ticket->price;
            else
                $subtotal = 0;
            $addonsSelected = [];
            
            if(($request->has('addons'))) {
                foreach($request->addons as $addonId) {
                    if(!$addon = $registrationPage->findAddon($addonId))
                        continue;
                    $addonsSelected[] = $addon;
                    $subtotal += $registrationPage->getAddonPrice($addonId);
                }
            }
            if($request->has('coupon_code') && $request->coupon_code != '') {
                $total = $registrationPage->getTotalWithCouponCode($request->coupon_code, $subtotal);
                $coupon_info = $registrationPage->getCouponInfo($request->coupon_code);
            }
            else
                $total = $subtotal;
            if ($total == 0 || ($total > 0 && $request->has('paymentMethod'))) {
                $isUserLoggedIn = Auth::check();
                if(!$isUserLoggedIn) {
                    $validated = $request->validate([
                        'name' => 'required',
                        'email' => 'required|string|email|max:255|unique:users',
                        'password' => 'required'
                    ]);
                }
               
                if(!$isUserLoggedIn) {
                    $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => bcrypt($request->input('password')),
                        'locale' => $request->has('locale') ? $request->locale : 'en',
                        'is_hidden' => getsetting('hide_new_members'),
                        'is_event_only' => $registrationPage->is_event_only,
                    ]);
                    \Auth::login($user);
                    event(new \Illuminate\Auth\Events\Registered($user));
                }
                else
                    $user = $request->user();
                \Stripe\Stripe::setApiKey(get_stripe_credentials('secret'));
                //come up with price
                $paymentMethod = $request->paymentMethod;
                if($total > 0) {
                    $totalToCharge = floor($total * 100);
                    try {
                        //amount is in cents
                         $payment = $user->charge($totalToCharge, $paymentMethod);
                    } catch (Exception $e) {

                        //throw $e;
                        return redirect()->back()->withErrors(['msg' => 'Invalid payment information.']);
                    } catch(\Stripe\Exception\CardException $e) {
                        return redirect()->back()->withErrors(['msg' => 'Your card was declined.']);
                        //throw $e;
                    }
                }
                $access_granted = [];
                if($registrationPage->assign_to_groups)
                {
                    $user->groups()->attach($registrationPage->assign_to_groups);
                    foreach($registrationPage->assign_to_groups as $groupId)
                    {
                        $access_granted[] = $groupId;
                    }
                }
                if($request->has('ticket')) {
                    $ticket = Ticket::find($request->ticket);
                    if($ticket->add_to_groups)
                    {
                        $user->groups()->syncWithoutDetaching($ticket->add_to_groups);
                        foreach($ticket->add_to_groups as $groupId)
                            $access_granted[] = $groupId;
                    }
                }
                //create receipt
                $details = [];
                if(isset($coupon_info)) {
                    $details['coupon'] = [
                        'code' => $request->coupon_code,
                        'label' => $coupon_info['label'],
                    ];
                }
                if(count($addonsSelected))
                    $details['addons'] = $addonsSelected;
                //store ticket just incase they change the ticket price
                if(isset($ticket))
                    $details['ticket'] = $ticket;

                $receipt = Receipt::create([
                    'ticket_id' => isset($ticket) ? $ticket->id : null,
                    'register_page_id' => $registrationPage->id ? $registrationPage->id  : null,
                    'access_granted' => $access_granted,
                    'amount_paid' => floor($total * 100),
                    'details' => $details,
                    'user_id' => $user->id,
                ]);
                if ($total > 0)
                    event(new PaymentProcessed($user, $receipt));
                // Handle Specialized Access Code
                if ($request->has('access_code') && $request->access_code != '') {
                    if($accessCodeGroup = Group::where('join_code', '=', $request->access_code)->first()) {
                        $user->groups()->attach($accessCodeGroup);
                        //join parent groups if not a member
                        if($accessCodeGroup->parent)
                        {
                            $groupToJoin = $accessCodeGroup->parent;
                            while(isset($groupToJoin))
                            {
                                $user->groups()->attach($groupToJoin->id);
                                if(!$groupToJoin->parent_group_id)
                                    break;
                                $groupToJoin = $groupToJoin->parent;
                            }
                        }
                        $user->logs()->create([
                            'action' => 'joined group via specialized access code: ' . $request->access_code,
                        ]);
                        if ($accessCodeGroup->is_welcome_message_enabled && $accessCodeGroup->welcome_message_sending_user_id && $accessCodeGroup->welcome_message) {
                            $sendingUser = User::find($accessCodeGroup->welcome_message_sending_user_id);
                            $participants = [
                                $sendingUser,
                                $user,
                            ];
                            $thread = MessageThread::create();
                            $thread->participants()->saveMany($participants);
                            $message = $thread->messages()->create([
                                'sending_user_id' => $sendingUser->id,
                                'message' => $accessCodeGroup->welcome_message,
                            ]);
                            event(new MessageSent($sendingUser, $thread, $message));
                        }
                    }
                }
                if($registrationPage->event_date && $registrationPage->event_end_date && $registrationPage->event_name) {
                    DB::commit();
                    return redirect('/addToCalendar?page=' . $registrationPage->id);
                }
            } else {
                return redirect()->back()->withErrors(['msg' => 'Something went wrong, try again. Please temporarily disable your security apps and popup blockers.']);
            }

            DB::commit();
        } catch (\Exception $e) {
            if(!empty($payment)){
                $refund =  $user->refund($payment->id);
            }
            DB::rollback();
            throw $e;
        }
        if(!$isUserLoggedIn)
             return redirect('/onboarding');
        else
             return redirect('/home');
    }

    public function openSignup(Request $request)
    {
        if ($request->has('lang') && $request->lang == 'es')
            App::setLocale('es');

        if(\App\Setting::where('name', 'open_registration')->first()->value)
            return view('auth.openregister');
        else
            return redirect('/register');
    }

    public function addToCalendar(Request $request)
    {
        $page = RegistrationPage::where('id', $request->page)->withTrashed()->orderBy('name', 'desc')->first();
        return view('auth.addToCalendar')->with([
            'page' => $page,
        ]);
    }
    //depreciated
    // public function createAccount(Request $request)
    // {
    //     $custom_validator_messages = [
    //         'email.unique' => 'This email address is already associated with a member account. Instead of creating another account, please <a href="/login">login using the email address and password</a>. Forgot your password? No problem!<a href="/password/reset"> Request a reset</a>. Suspect someone else has used your email address to create an account? <a href="/contact-us">Contact us</a>.'
    //     ];
    //     $validator = Validator::make($request->all(), [
    //                                         'name' => 'required',
    //                                         'email' => 'required|string|email|max:255|unique:users',
    //                                         'password' => 'required'
    //                                     ], $custom_validator_messages);
    //     if($validator->fails())
    //         return redirect(($request->has('locale')) ? '/signup?lang=' . $request->locale : '/signup')->withErrors($validator);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => bcrypt($request->input('password')),
    //         'locale' => $request->has('locale') ? $request->locale : 'en',
    //         'is_event_only' => getsetting('or_event_only_groups') ? 1 : 0,
    //         'is_hidden' => getsetting('hide_new_members'),
    //     ]);

    //     if (getSetting('or_event_only_groups')) {
    //         $user->groups()->sync(json_decode(getsetting('or_event_only_groups')));
    //     }

    //     if ($request->has('access_code') && $request->access_code != '') {
    //         if($accessCodeGroup = Group::where('join_code', '=', $request->access_code)->first()) {
    //             $user->groups()->attach($accessCodeGroup);
    //             $user->logs()->create([
    //                 'action' => 'joined group via specialized access code: ' . $request->access_code,
    //             ]);
    //             if ($accessCodeGroup->is_welcome_message_enabled && $accessCodeGroup->welcome_message_sending_user_id && $accessCodeGroup->welcome_message) {
    //                 $sendingUser = User::find($accessCodeGroup->welcome_message_sending_user_id);
    //                 $participants = [
    //                     $sendingUser,
    //                     $user,
    //                 ];
    //                 $thread = MessageThread::create();
    //                 $thread->participants()->saveMany($participants);
    //                 $message = $thread->messages()->create([
    //                     'sending_user_id' => $sendingUser->id,
    //                     'message' => $accessCodeGroup->welcome_message,
    //                 ]);
    //                 event(new MessageSent($sendingUser, $thread, $message));
    //             }
    //         }
    //     }

    //     \Auth::login($user);
    //     event(new \Illuminate\Auth\Events\Registered($user));

    //     return view('invitation.success');
    // }

    public function getPrice(Request $request)
    {
        parse_str($request->addons, $payment_info);
        if(!array_key_exists('ticket', $payment_info))
            return false;

        $ticket = Ticket::find($payment_info['ticket']);

        $price = 0;
        $price += $ticket->price;

        if($request->has('addons'));
        {
            $page = RegistrationPage::find($request->page);
            foreach($payment_info['addons'] as $addonId)
            {
                $price += $page->getAddonPrice($addonId);
            }
        }

        return response($price);
    }

    public function checkCoupon($slug, Request $request)
    {
        $page = RegistrationPage::where('slug', $slug)->first();

        $userEnteredCode = $request->code;
        $code = false;

        foreach($page->coupon_codes as $coupon_code)
        {
            if($coupon_code['code'] == $userEnteredCode)
            {
                $code = $coupon_code;
            }
        }

        if(!$code)
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

        return response()->json($response);
    }
}
