<?php

namespace App\Http\Controllers;

use App\Receipt;
use App\RegistrationPage;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('receipts.index')->with([
            'receipts' => $request->user()->receipts()->orderBy('id', 'desc')->get(),
        ]);
    }

    public function show($receiptId)
    {
        return view('receipts.show')->with([
            'receipt' => Receipt::find($receiptId),
        ]);
    }

    public function export($id)
    {
        set_time_limit(0);
        $receipt = Receipt::find($id);

        $page = RegistrationPage::find($receipt->register_page_id);

        $pdf = PDF::loadView('receipts.exportPurchase', ['receipt' => $receipt,'page' => $page,'isSimple' => false]);
        return $pdf->download('invoice - '. $receipt->id .'.pdf');

    }
}
