<?php

namespace App\Http\Controllers\Admin;

use App\EmailNotification;
use App\Helpers\EmailHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailNotificationController extends Controller
{
    protected $helper;

    public function __construct(EmailHelper $helper)
    {
        $this->helper = $helper;
    }

    public function index(Request $request)
    {
        return view('admin.emails.notifications.index')->with([
            'emails' => EmailNotification::all(),
        ]);
    }

    public function show(Request $request, $id)
    {
        $email = EmailNotification::find($id);

        return view('admin.emails.notifications.show')->with([
            'email' => $email,
        ]);
    }

    public function edit(Request $request, $id)
    {
        return view('admin.emails.notifications.edit')->with([
            'email' => EmailNotification::find($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $email = EmailNotification::find($id);
        if($request->has('locale') && $request->locale != 'en')
        {
            $localization = $email->localization;
            $localization[$request->locale]['email_html'] = $request->input('html');
            $localization[$request->locale]['subject'] = $request->input('subject');
            $localization[$request->locale]['email_template'] = $request->input('template');
            $email->update(['localization' => $localization]);

            return redirect("/admin/emails/notifications/{$id}?locale=".$request->locale);
        }
        else
        {
            $email->update([
                'subject' => $request->input('subject'),
                'email_html' => $request->input('html'),
                'email_template' => $request->input('template'),
                'is_enabled' => ($request->has('enabled')) ? 1 : 0,
             ]);

        }
        return redirect("/admin/emails/notifications/{$id}");
    }

    public function getTemplate(Request $request, $id)
    {
        $email = EmailNotification::find($id);

        if($request->has('locale') && isset($email->localization[$request->locale]['email_template']) && $request->locale != 'en' && getsetting('is_localization_enabled'))
            $template = $email->localization[$request->locale]['email_template'];
        else
            $template = $email->email_template;

        $template = $this->helper->replaceColors($template);
        $template = $this->helper->replaceYear($template);

        $template = preg_replace('/(<re-container>\s*<re-card)( .*)(>)/', '<re-container ${2}>', $template);
        $template = str_replace('</re-card>', '', $template);

        if ($template)
            return $template;
        else
            return $this->helper->replaceColors(file_get_contents('./revolvapp-2-3-2/templates/index.html'));
    }

    public function getHtml(Request $request, $id)
    {
        $email = EmailNotification::find($id);

        if(isset($email->localization[$request->locale]['email_html']) && $request->has('locale') && $request->locale != 'en' && getsetting('is_localization_enabled'))
            $html = $email->localization[$request->locale]['email_html'];
        else
            $html = $email->email_html;

        $html = $this->helper->replaceYear($html);

        return $this->helper->replaceColors(
            $this->helper->replaceCtaWith(
                $html, config('app.url').'/#it-works'
            )
        );
    }

    
}
