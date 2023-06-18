<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\EmailHelper;
use App\Http\Controllers\Controller;
use App\WelcomeEmail;
use Illuminate\Http\Request;

class EmailWelcomeController extends Controller
{
    protected $helper;

    public function __construct(EmailHelper $helper)
    {
        $this->helper = $helper;
    }

    public function index()
    {
        return view('admin.emails.welcome.index')->with([
            'emails' => WelcomeEmail::orderBy('send_after_days', 'asc')->get()
        ]);
    }

    public function show($id)
    {
        return view('admin.emails.welcome.show')->with([
            'email' => WelcomeEmail::find($id),
        ]);
    }

    public function edit($id)
    {
        return view('admin.emails.welcome.edit')->with([
            'email' => WelcomeEmail::find($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'email_subject' => 'required',
            'send_after_days' => 'required',
        ]);

         WelcomeEmail::where('id', '=', $id)
                     ->update([
                         'email_subject' => $request->input('email_subject'),
                         'email_html' => $request->input('html'),
                         'email_template' => $request->input('template'),
                         'send_after_days' => $request->input('send_after_days'),
                         'enabled' => $request->has('enabled'),
                     ]);

        return redirect("/admin/emails/welcome/{$id}");
    }

    public function create()
    {
        return view('admin.emails.welcome.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email_subject' => 'required',
            'send_after_days' => 'required',
        ]);
        
        $email = WelcomeEmail::create([
            'email_subject' => $request->input('email_subject'),
            'send_after_days' => $request->input('send_after_days'),
            'email_html' => $request->input('html'),
            'email_template' => $request->input('template'),
        ]);

        return redirect("/admin/emails/welcome/{$email->id}");
    }

    public function getTemplate(Request $request, $id)
    {
        $template = $this->helper->replaceColors(WelcomeEmail::find($id)->email_template);
        $template = $this->helper->replaceYear($template);

        return $template;
    }

    public function getHtml(Request $request, $id)
    {
        $html = $this->helper->replaceYear(WelcomeEmail::find($id)->email_html);

        return $this->helper->replaceColors($html);
    }

    public function delete(Request $request, $id)
    {
        WelcomeEmail::find($id)->delete();

        return redirect("/admin/emails/welcome/");
    }
    
}
