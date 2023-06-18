<?php

namespace App\Http\Controllers\Group;

use App\Group;
use App\Http\Controllers\Controller;
use App\SequenceReminder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Jobs\Notifications\SequenceReminder as SequenceReminderJob;

class SequenceReminderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'group']);
    }
    
    public function index(Group $group, Request $request)
    {
        return view('groups.sequence.reminders.index')->with([
            'group' => $group,
            'reminders' => $group->sequence->reminders()->orderBy('send_after_days', 'asc')->get(),
        ]);
    }

    public function create(Group $group, Request $request)
    {
        return view('groups.sequence.reminders.create')->with([
            'group' => $group,
        ]);
    }

    public function store(Group $group, Request $request)
    {
        $request->validate([
            'email_subject' => 'required',
            'send_after_days' => 'required',
        ]);

        $reminder = SequenceReminder::create([
            'sequence_id' => $group->sequence->id,
            'subject' => $request->email_subject,
            'send_after_days' => $request->send_after_days,
            'template' => $request->template,
            'html' => $request->html,
            'is_enabled' => $request->has('is_enabled'),
        ]);

        SequenceReminderJob::dispatch($reminder, $reminder->send_after_days)->delay(Carbon::now()->addDays($reminder->send_after_days));

        return redirect('/groups/'.$group->slug.'/sequence/reminders');
    }

    public function show(Group $group, SequenceReminder $reminder, Request $request)
    {
        return view('groups.sequence.reminders.show')->with([
            'group' => $group,
            'reminder' => $reminder,
        ]);
    }

    public function edit(Group $group, SequenceReminder $reminder, Request $request)
    {
        return view('groups.sequence.reminders.edit')->with([
            'group' => $group,
            'reminder' => $reminder,
        ]);
    }

    public function update(Group $group, SequenceReminder $reminder, Request $request)
    {
        $reminder->update([
            'sequence_id' => $group->sequence->id,
            'subject' => $request->email_subject,
            'send_after_days' => $request->send_after_days,
            'template' => $request->template,
            'html' => $request->html,
            'is_enabled' => $request->has('is_enabled'),
        ]);

        SequenceReminderJob::dispatch($reminder, $reminder->send_after_days)->delay(Carbon::now()->addDays($reminder->send_after_days));

        return redirect('/groups/'.$group->slug.'/sequence/reminders');
    }

    public function template(Group $group, SequenceReminder $reminder, Request $request)
    {
        // preg_replace() and str_replace() below are necessary to get
        // RevolveApp 1.0 templates to load correctly since updating to RevolveApp 2.3
        $template = preg_replace('/(<re-container>\s*<re-card)( .*)(>)/', '<re-container ${2}>', $reminder->template);
        $template = str_replace('</re-card>', '', $template);
        
        return $template;
    }

}
