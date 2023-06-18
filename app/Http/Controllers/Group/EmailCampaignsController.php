<?php

namespace App\Http\Controllers\Group;

use App\EmailCampaign;
use App\Group;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmailCampaignsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'group']);
    }
    
    public function index(Group $group, Request $request)
    {
        return view('groups.campaigns.index')->with([
            'group' => $group,
            'campaigns' => $group->emailCampaigns()->orderBy('id', 'desc')->get(),
        ]);
    }

    public function create(Group $group, Request $request)
    {
        return view('groups.campaigns.create')->with([
            'group' => $group,
        ]);
    }

    public function store(Group $group, Request $request)
    {
        EmailCampaign::create([
            'email_subject' => $request->input('email_subject'),
            'email_html' => $request->input('html'),
            'email_template' => $request->input('template'),
            'created_by_user' => $request->user()->id,
            'status' => 'not sent',
            'group_id' => $group->id,
            'reply_to_email' => $request->reply_to_email,
        ]);

        return redirect("/groups/{$group->slug}/email-campaigns")->with([
            'success' => 'Success: Email campaign has been created.',
        ]);
    }

    public function show(Group $group, EmailCampaign $campaign, Request $request)
    {
        return view('groups.campaigns.show')->with([
            'group' => $group,
            'campaign' => $campaign,
        ]);
    }

    public function edit(Group $group, EmailCampaign $campaign, Request $request)
    {
        return view('groups.campaigns.edit')->with([
            'group' => $group,
            'campaign' => $campaign,
        ]);
    }

    public function update(Group $group, EmailCampaign $campaign, Request $request)
    {
        $campaign->update([
            'email_subject' => $request->input('email_subject'),
            'email_html' => $request->input('html'),
            'email_template' => $request->input('template'),
            'status' => 'not sent',
            'reply_to_email' => $request->reply_to_email,
        ]);

        return redirect("/groups/{$group->slug}/email-campaigns")->with([
            'success' => 'Success: Email campaign has been saved.',
        ]);;;
    }

    public function selectRecipients(Group $group, EmailCampaign $campaign, Request $request)
    {
        return view('groups.campaigns.selectrecipients')->with([
            'group' => $group,
            'campaign' => $campaign,
        ]);
    }

    public function schedule(Group $group, EmailCampaign $campaign, Request $request)
    {
        return view('groups.campaigns.schedule')->with([
            'group' => $group,
            'campaign' => $campaign,
        ]);
    }

    public function review(Group $group, EmailCampaign $campaign, Request $request)
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

        return view('groups.campaigns.review')->with([
            'group' => $group,
            'campaign' => $campaign,
        ]);
    }

    public function send(Group $group, EmailCampaign $campaign, Request $request)
    {
        $campaign->update([
            'sent_at' => null,
            'sent_by_user' => $request->user()->id,
            'status' => ($campaign->send_at) ? 'scheduled' : 'queued',
        ]);

        return redirect("/groups/{$group->slug}/email-campaigns")->with([
            'success' => 'Success: Campaign is being processed to be sent.',
        ]);
    }

    public function html(Group $group, EmailCampaign $campaign, Request $request)
    {
        return $campaign->email_html;
    }

    public function template(Group $group, EmailCampaign $campaign, Request $request)
    {
        // preg_replace() and str_replace() below are necessary to get
        // RevolveApp 1.0 templates to load correctly since updating to RevolveApp 2.3
        $template = preg_replace('/(<re-container>\s*<re-card)( .*)(>)/', '<re-container ${2}>', $campaign->email_template);
        $template = str_replace('</re-card>', '', $template);
        
        return $template;
    }
}
