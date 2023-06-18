<?php

namespace App\Http\Controllers\Admin;

use Cache;
use App\User;
use App\Group;
use App\Ticket;
use App\Receipt;
use App\Setting;
use Carbon\Carbon;
use App\RegistrationPage;
use FontLib\Table\Type\name;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Str;
class RegisterController extends Controller
{
    public $addonsName;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = RegistrationPage::orderBy('created_at', 'desc')->withTrashed()->get();

        return view('admin.registration.index')->with([
            'pages' => $pages,
            'image' => Setting::where('name', 'pick_registration_image_url')->first(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
        return view('admin.registration.create')->with([
            'groups' => Group::whereNull('parent_group_id')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:registration_pages',
        ]);

        $localization = $request->localization;

        if($request->has('image_url_localization'))
            $localization['es']['image_url'] = $request->image_url_localization['es']['image_url']->store('images', 's3');

        $page = RegistrationPage::create([
            'name' => $request->name,
            'slug' => $request->slug.Str::random(25),
            'description' => $request->description,
            'is_welcome_page_accessible' => $request->has('is_welcome_page_accessible'),
            'is_event_only' => $request->is_event_only,
            'assign_to_groups' => $request->has('groups') ? $request->groups : null,
            'localization' => $localization,
            'prompt' => $request->prompt,
            'image_url' => $request->has('image_url') ? $request->image_url->store('images', 's3') : '',
            'event_date' => $request->event_date ? Carbon::parse($request->event_date . ' ' . $request->event_time) : null,
            'event_end_date' => $request->event_end_date ? Carbon::parse($request->event_end_date . ' ' . $request->event_end_time) : null,
            'event_name' => $request->event_name,
        ]);

        return redirect('/admin/registration/' . $page->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('admin.registration.show')->with([
            'page' => RegistrationPage::withTrashed()->find($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admin.registration.edit')->with([
            'page' => RegistrationPage::find($id),
            'groups' => Group::whereNull('parent_group_id')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:registration_pages,slug,' . $id,
        ]);

        $page = RegistrationPage::find($id);

        $localization = $request->localization;

        if($request->has('image_url_localization'))
            $localization['es']['image_url'] = $request->image_url_localization['es']['image_url']->store('images', 's3');
        else
            $localization['es']['image_url'] = isset($page->localiation['es']) && isset($page->localization['es']['image_url']) ? $page->localization['es']['image_url'] : '';

        $addons = null;
        $coupon_codes = null;
        if($request->has('addons'))
        {
            $addons = [];
            foreach($request->addons as $index => $addon)
            {
                if(!array_key_exists('price', $addon) || !array_key_exists('name', $addon))
                    continue;

                $price = str_replace([',','$'], '',$addon['price']);

                if(!is_numeric($price))
                    return back()->withErrors(['msg' => 'Prices must be numeric.']);

                $addons[] = [
                    'id' => $index + 1,
                    'name' => $addon['name'],
                    'price' => $price * 100,
                    'description' => array_key_exists('description', $addon) ? $addon['description'] : '',
                ];
            }
        }
        if($request->has('coupon_codes'))
        {
            $coupon_codes = [];
            foreach($request->coupon_codes as $code)
            {
                if(!array_key_exists('amount', $code) || !array_key_exists('type', $code) || !array_key_exists('code', $code))
                    continue;

                $amount = str_replace([',','.','$', '%'], '',$code['amount']);
                if(!is_numeric($amount))
                    return redirect()->back()->withErrors(['msg' => 'Coupon code amount must be numeric']);

                $coupon_codes[] = [
                    'code' => $code['code'],
                    'amount' => $amount * 100,
                    'type' => $code['type'],
                ];
            }
        }

        $page->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'is_welcome_page_accessible' => $request->has('is_welcome_page_accessible'),
            'is_event_only' => $request->is_event_only,
            'assign_to_groups' => $request->has('groups') ? $request->groups : null,
            'localization' => $localization,
            'prompt' => $request->prompt,
            'event_date' => $request->event_date ? Carbon::parse($request->event_date . ' ' . $request->event_time) : null,
            'event_end_date' => $request->event_end_date ? Carbon::parse($request->event_end_date . ' ' . $request->event_end_time) : null,
            'event_name' => $request->event_name,
            'addons' => $addons,
            'coupon_codes' => $coupon_codes,
            'purchased_warning_title' => ($request->purchased_warning_title) ?? $page->purchased_warning_title,
            'purchased_warning_message' => ($request->purchased_warning_message) ?? $page->purchased_warning_message,
            'purchased_warning_url' => ($request->purchased_warning_url) ?? $page->purchased_warning_url,
            'purchased_warning_button_text' => ($request->purchased_warning_button_text) ?? $page->purchased_warning_button_text,
        ]);

        if($request->has('ticket_prompt'))
        {
            $page->update([
                'ticket_prompt' => $request->ticket_prompt,
                'addon_prompt' => $request->addon_prompt,
            ]);
        }

        if($request->has('image_url') && !$request->has('image_url_remove'))
        {
            $page->update([
                'image_url' => $request->has('image_url') ? $request->image_url->store('images', 's3') : '',
            ]);
        }
        elseif($request->has('image_url_remove'))
        {
            $page->update([
                'image_url' => '',
            ]);
        }

        return redirect('/admin/registration/' . $id);
    }

    public function changeStatus($receiptId, Request $request)
    {
        $request->validate([
            'status' => 'required',
        ]);

        $receipt = Receipt::find($receiptId);

        if(!$receipt)
            return redirect()->back();

        $receipt->update(['status' => $request->status]);

        if($request->has('remove_access'))
            $request->user()->groups()->detach($receipt->access_granted);

        $registrationPageId = $receipt->ticket->registration_page_id;

        return redirect('/admin/registration/'.$registrationPageId.'/purchases/'.$receiptId);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        RegistrationPage::find($id)->delete();

        return redirect('/admin/registration');
    }

    public function uploadImage(Request $request)
    {
        $image = Setting::where('name', 'pick_registration_image_url')->first();
        Setting::where('name', 'pick_registration_image_url')->update([
            'value' => $request->has('pick_registration_image_url') ? $request->pick_registration_image_url->store('images', 's3') : $image->value,
            'localization' => $request->has('pick_registration_image_url_localization') ? ['es' => ['pick_registration_image_url' =>  $request->pick_registration_image_url_localization['es']['pick_registration_image_url']->store('images', 's3')]] : $image->localization,
        ]);

        Cache::forget('settings');

        return redirect('/admin/registration');
    }

    public function indexTickets($id)
    {
        return view('admin.registration.tickets.index')->with([
            'page' => RegistrationPage::withTrashed()->find($id),
        ]);
    }

    public function showTicket($id, $ticketId)
    {
        return view('admin.registration.tickets.show')->with([
            'page' => RegistrationPage::withTrashed()->find($id),
            'ticket' => Ticket::find($ticketId),
        ]);
    }

    public function newTicket($id)
    {
        return view('admin.registration.tickets.create')->with([
            'page' => RegistrationPage::withTrashed()->find($id),
        ]);
    }

    public function storeTicket($id, Request $request)
    {
        $request->validate([
            'price' => 'required',
            'name' => 'required',
        ]);

        $page = RegistrationPage::withTrashed()->find($id);
        $price = str_replace([',','$'], '',$request->price);
        if(!is_numeric($price))
            return redirect()->back()->withErrors(['msg' => 'Price must be numeric.']);

        $ticket = $page->tickets()->create([
            'price' => (int) ($price * 100),
            'name' => $request->name,
            'add_to_groups' => $request->groups,
            'description' => $request->description,
        ]);

        return redirect('/admin/registration/'.$id.'/tickets/'.$ticket->id);
    }

    public function editTicket($registrationPageId, $ticketId, Request $request)
    {
        return view('admin.registration.tickets.edit')->with([
            'page' => RegistrationPage::withTrashed()->find($registrationPageId),
            'ticket' => Ticket::find($ticketId),
        ]);
    }

    public function updateTicket($ticketId,$id, Request $request)
    {
        $request->validate([
            'price' => 'required',
            'name' => 'required',
        ]);

        $price = str_replace([',','$'], '',$request->price);
        if(!is_numeric($price))
            return redirect()->back()->withErrors(['msg' => 'Price must be numeric.']);

        $ticket = Ticket::find($id);
        $ticket->update([
            'name' => $request->name,
            'price' => (int) ($price * 100),
            'add_to_groups' => $request->groups,
            'description' => $request->description,
        ]);

        return redirect()->back();
    }

    public function indexPurchases($rpId)
    {
        $page = RegistrationPage::withTrashed()->find($rpId);
        $tickets = $page->tickets()->pluck('id');
        $query = Receipt::query();
        $query->where('register_page_id', $page->id)
                ->orWhereIn('ticket_id', $tickets)
                ->where('details', '!=', '[]')
                ->orderBy('created_at', 'desc');
        $receipts = $query->get();

        return view('admin.registration.purchases.index')->with([
            'page' => $page,
            'receipts' => $receipts,
        ]);
    }
    public function indexRegisterReport($rpId)
    {
        $page = RegistrationPage::withTrashed()->find($rpId);
        $tickets = $page->tickets()->pluck('id');
        $query = Receipt::query();
        $query->where('register_page_id', $page->id)
                ->orWhereIn('ticket_id', $tickets)
                ->where('details', '!=', '[]')
                ->orderBy('created_at', 'desc');
        $receipts = $query->get();
        return view('admin.registration.couponCode.index')->with([
            'page' => $page,
            'receipts' => $receipts,
        ]);
    }

    public function exportRegisterReport($rpId)
    {
        set_time_limit(0);
        $pages = RegistrationPage::withTrashed()->find($rpId);
        $tickets = $pages->tickets()->pluck('id');
        $query = Receipt::query();
        $query->where('register_page_id', $pages->id)
                ->orWhereIn('ticket_id', $tickets)
                ->where('details', '!=', '[]')
                ->orderBy('created_at', 'desc');
        $receipts = $query->get();




        foreach($receipts as $key => $receipt) {
            $couponExists = isset($receipt->details['coupon']);
            $addonExists = isset($receipt->details['addons']);

            if($couponExists || $addonExists){
                if($couponExists){
                    $couponType = $receipt->details['coupon']['label'];
                    $getPercentage  = explode(" " ,$couponType);
                    if (strpos($receipt->details['coupon']['label'], '%') !== false) {
                        $receipts[$key]->coupon_type = 'Percentage';
                    }else if(strpos($receipt->details['coupon']['label'], '$') !== false){
                        $receipts[$key]->coupon_type = 'Fixed';
                    }else{
                        $receipts[$key]->coupon_type = '---';
                    }
                }
                if(isset($receipt->details['addons'])){
                    $addonArr = [];
                    foreach($receipt->details['addons'] as $addon){
                        array_push($addonArr, $addon['name'] . '-' . $addon['price']);
                    }
                    $receipts[$key]->addonsData = implode(',', $addonArr);
                }

                if(isset($receipt->details['ticket'])){
                    $ticketArr = [];
                    if(isset($receipt->details['addons'])){
                        foreach($receipt->details['addons'] as $ticket){
                            array_push($ticketArr, $ticket['name']);
                        }
                        $receipts[$key]->ticketData = implode(',', $ticketArr);
                    }
                }
            }
        }
        $this->addonsName = (RegistrationPage::find($rpId)) ? RegistrationPage::find($rpId)['addons'] : [];
        return (new FastExcel($receipts))->download($pages->name.'-used-coupon-code.csv', function ($receipt) {

            $rowData = [
                'Name' => $receipt->user->name,
                'Email' => $receipt->user->email,
                'Company' =>  ($receipt->user->company) ?? '-',
                'Title' =>  ($receipt->user->job_title) ?? '-' ,
                'Type of Ticket' => ($receipt->details['ticket']['name']) ?? '-',
                'Coupon Code' =>  ($receipt->details['coupon']['code']) ?? '-',
                'Coupon Type' =>  ($receipt->coupon_type) ?? '-',
                'Coupon Amount' => ($receipt->details['coupon']['label']) ?? '-',
                'Paid Amount' =>  number_format($receipt->amount_paid,2),
                'Date' => $receipt->created_at ? $receipt->created_at->format('Y-m-d') : '',
                'Status' => $receipt->status,

            ];
            foreach ($this->addonsName as $key => $addon) {
                if($receipt->addonsData && $receipt->addonsData != null) {
                    $hasAddon = str_contains($receipt->addonsData, $addon['name']);
                    if($hasAddon) {
                        if($addon['price'] > 0){
                            $price = '$'.$addon['price'];
                        }elseif ($addon['price'] == 0){
                            $price = 'YES';
                        }
                    } else {
                        $price = '';
                    }
                } else {
                    $price = '';
                }
                $rowData[$addon['name']] = $price;
            }
            return $rowData;
        });
    }

    public function showPurchase($rpId, $receiptId)
    {

        return view('admin.registration.purchases.show')->with([
            'page' => RegistrationPage::find($rpId),
            'receipt' => Receipt::find($receiptId),
        ]);
    }

    public function exportPurchases($regPageId,Request $request)
    {

        set_time_limit(0);
        $page = RegistrationPage::find($regPageId);
        $tickets = $page->tickets()->pluck('id');
        $query = Receipt::query();
        $receiptid = explode(',',$request->ids);
        if($request->ids != ''){
            $query->WhereIn('id',$receiptid)

                ->orderBy('created_at', 'desc');
        }else{
            $query->where('register_page_id', $page->id)
                ->orWhereIn('ticket_id', $tickets)
                ->where('details', '!=', '[]')
                ->orderBy('created_at', 'desc');
        }
        $receipts = $query->get();
        $pdf = PDF::loadView('receipts.export-many', ['receipts' => $receipts, 'page' => $page, 'is_editable' => false]);
        return $pdf->download('invoices - '. $page->id .'.pdf');
    }
    public function deleteTicket($pageId, $ticketId)
    {
        Ticket::find($ticketId)->delete();
        return redirect('/admin/registration/'.$pageId.'/tickets');
    }

    public function dashboardSetting(Request $request)
    {
      
        return view('admin.system.admin-dashboard-setting')->with([
            'admin_primary_color' => Setting::where('name', 'admin_primary_color')->first(),  
            'admin_btn_secondary' => Setting::where('name', 'admin_btn_secondary')->first(),  
            'admin_btn-success' => Setting::where('name', 'admin_btn-success')->first(),  
            'admin_btn_danger' => Setting::where('name', 'admin_btn_danger')->first(),  
            'admin_btn_info' => Setting::where('name', 'admin_btn_info')->first(),  
            'admin_btn_light' => Setting::where('name', 'admin_btn_light')->first(),  
            'admin_btn_dark' => Setting::where('name', 'admin_btn_dark')->first(),  
            'admin_btn_warning' => Setting::where('name', 'admin_btn_warning')->first(),  
          
          
        ]);
    }
    public function UpdateDashboardSetting(Request $request)
    {
        
        Setting::where('name', 'admin_primary_color')->update(['value' => $request->input('admin_primary_color')]);
        Setting::where('name', 'admin_btn_secondary')->update(['value' => $request->input('admin_btn_secondary')]);
        Setting::where('name', 'admin_btn_success')->update(['value' => $request->input('admin_btn_success')]);
        Setting::where('name', 'admin_btn_danger')->update(['value' => $request->input('admin_btn_danger')]);
        Setting::where('name', 'admin_btn_info')->update(['value' => $request->input('admin_btn_info')]);
        Setting::where('name', 'admin_btn_warning')->update(['value' => $request->input('admin_btn_warning')]);
        Setting::where('name', 'admin_btn_light')->update(['value' => $request->input('admin_btn_light')]);
        Setting::where('name', 'admin_btn_dark')->update(['value' => $request->input('admin_btn_dark')]);


        return redirect()->back()->with('success', 'Settings saved!');
    }
}
