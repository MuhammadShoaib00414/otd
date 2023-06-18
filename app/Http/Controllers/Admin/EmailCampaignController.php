<?php

namespace App\Http\Controllers\Admin;

use App\EmailCampaign;
use App\Helpers\EmailHelper;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmailCampaignController extends Controller
{
    protected $helper;

    public function __construct(EmailHelper $helper)
    {
        $this->helper = $helper;
    }

    public function index(Request $request)
    {
        return view('admin.emails.campaigns.index')->with([
            'campaigns' => EmailCampaign::orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.emails.campaigns.create');
    }

    public function store(Request $request)
    {
        EmailCampaign::create([
            'email_subject' => $request->input('email_subject'),
            'email_html' => $request->input('html'),
            'email_template' => $request->input('template'),
            'created_by_user' => $request->user()->id,
            'status' => 'not sent',
            'reply_to_email' => $request->reply_to_email,
        ]);

        return redirect('/admin/emails/campaigns');
    }

    public function show(Request $request, $id)
    {
        return view('admin.emails.campaigns.show')->with([
            'campaign' => EmailCampaign::find($id),
        ]);
    }

    public function edit(Request $request, $id)
    {
        return view('admin.emails.campaigns.edit')->with([
            'campaign' => EmailCampaign::find($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        EmailCampaign::where('id', '=', $id)
                     ->update([
                         'email_subject' => $request->input('email_subject'),
                         'email_html' => $request->input('html'),
                         'email_template' => $request->input('template'),
                         'reply_to_email' => $request->reply_to_email,
                     ]);

        return redirect("/admin/emails/campaigns");
    }

    public function getTemplate(Request $request, $id)
    {
        // preg_replace() and str_replace() below are necessary to get
        // RevolveApp 1.0 templates to load correctly since updating to RevolveApp 2.3
        $template = $this->helper->replaceColors(EmailCampaign::find($id)->email_template);
        $template = preg_replace('/(<re-container>\s*<re-card)( .*)(>)/', '<re-container ${2}>', $template);
        $template = str_replace('</re-card>', '', $template);
        
        return $template;
    }

    public function getHtml(Request $request, $id)
    {
        return $this->helper->replaceColors(EmailCampaign::find($id)->email_html);
    }

    public function send(Request $request, $id)
    {
        return view('admin.emails.campaigns.send')->with([
            'campaign' => EmailCampaign::find($id),
        ]);
    }

    public function schedule(Request $request, $id)
    {
        return view('admin.emails.campaigns.schedule')->with([
            'campaign' => EmailCampaign::find($id),
        ]);
    }

    public function review(Request $request, EmailCampaign $campaign)
    {
        $request->validate([
            'groups' => 'required_without:users',
            'users' => 'required_without:groups',
        ]);
        
        $campaign->update([
            'sent_to_details' => json_encode($request->only(['groups', 'users'])),
        ]);
        if($request->has('date') && $request->has('time')) {
            $timezonedSendAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($request->date . ' ' . $request->time), $request->user()->timezone);
            $campaign->update([
                'send_at' => $timezonedSendAt->tz('UTC'),
            ]);
        }

        return view('admin.emails.campaigns.review')->with([
            'campaign' => $campaign,
        ]);
    }

    public function postSend(Request $request, $id)
    {
        $campaign = EmailCampaign::find($id);

        $campaign->update([
            'sent_by_user' => $request->user()->id,
            'status' => ($campaign->send_at) ? 'scheduled' : 'queued',
        ]);
        
        return redirect("/admin/emails/campaigns/{$id}");
    }

    public function duplicate(Request $request, EmailCampaign $campaign)
    {
        $newCampaign = $campaign->replicate()->fill([
            'status' => 'not sent',
            'sent_by_user' => null,
            'sent_at' => null,
            'open_total' => null,
            'total_sent' => 0,
        ])->save();

        return redirect("/admin/emails/campaigns");
    }

    public function delete(EmailCampaign $campaign)
    {
        $campaign->delete();

        return redirect("/admin/emails/campaigns");
    }

}
