<?php

namespace App\Http\Controllers\Admin;

use App\Ticket;
use App\RegistrationPage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        return view('admin.tickets.index')->with([
            'tickets' => Ticket::orderBy('id', 'desc')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.tickets.create')->with([
            'registration_pages' => RegistrationPage::whereNull('ticket_id')->orderBy('name', 'asc')->get(),
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
            'price' => 'required|integer',
        ]);   

        $addons = [];
        if($request->has('addons'))
        {
            for($i = 0; $i < count($request->addons) / 2; $i+=2)
            {
                $addon = [
                    'name' => $request->addons[$i]['name'],
                    'price' => $request->addons[$i + 1]['price'],
                ];

                $addons[] = $addon;
            }
        }

        $coupons = [];
        if($request->has('coupons'))
        {
            for($i = 0; $i < count($request->coupons) / 3; $i+=3)
            {
                $coupon = [
                    'name' => $request->coupons[$i]['code'],
                    'amount' => $request->coupons[$i + 2]['amount'],
                    'type' => $request->coupons[$i + 1]['type'],
                ];

                $coupons[] = $coupon;
            }
        }

        $ticket = Ticket::create([
            'name' => $request->name,
            'price' => $request->price,
            'addons' => $addons,
            'coupon_codes' => $coupons,
        ]);

        if($request->has('registration_page') && $request->registration_page != '')
            RegistrationPage::where('id', $request->registration_page)->update(['ticket_id' => $ticket->id]);

        return redirect('/admin/tickets/'.$ticket->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket)
    {
        return view('admin.tickets.show')->with([
            'ticket' => $ticket,
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
