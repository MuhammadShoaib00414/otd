<?php

namespace App\Helpers;

use App\User;

class EmailHelper
{
    public function replaceSystemTags($html)
    {
        $replacements = [
            '@url' => config('app.url'),
            'https://onthedotconnects.com' => config('app.url'),
            'http://onthedotconnects.com' => config('app.url'),
            'onthedotconnects.com' => config('app.url'),
            '/images/logo-2.png' => config('app.url').'/images/logo-2.png',
            'https://onthedotconnects.com/images/logo-dark.png' => config('app.url').'/images/logo-2.png',
            'Unsubscribe' => '',
            '2018 On The Dot' => date('Y') . ' On The Dot',
            '@year' => date('Y'),
        ];

        return strtr($html, $replacements);
    }


    public function replaceTagsForUser($html, User $user)
    {
        $replacements = [
            '@name' => $user->name,
            '@email' => $user->email,
        ];

        return strtr($html, $replacements);
    }

    public function replaceReceiptWith($html, $view)
    {
        $replacements = ['@receipt' => $view];

        return strtr($html, $replacements);
    }

    public function replaceTagsForUserName($html, $name)
    {
        $replacements = [
            '@name' => $name,
        ];

        return strtr($html, $replacements);
    }
    public function replaceTagsForComment($html, $comment)
    {
        $replacements = [
            '@comment' => $comment,
        ];

        return strtr($html, $replacements);
    }

    public function replaceCtaWith($html, $replacement)
    {
        return str_replace('@cta', $replacement, $html);
    }

    public function addTrackers($html, $email)
    {
        return str_replace('</body>', '<img src="/open/'.get_class($email).'/'.$email->id);
    }

    public function replaceGroupNameWith($html, $replacement)
    {
        return str_replace('@groupName', $replacement, $html);
    }

    public function replaceTagsForReportedBy($html, $replacement)
    {
        return str_replace('@reportedBy', $replacement, $html);
    }

    public function replaceTagsForReportedUser($html, $replacement)
    {
        return str_replace('@reportedUser', $replacement, $html);
    }

    public function replaceCustomInvitationMessage($html, $replacement)
    {
        return str_replace('@custom_message', $replacement, $html);
    }

    public function replaceNotificationsWithFeed($html, $user)
    {
        return str_replace('@notifications', view('emails.components.notifications.feed')->with(['notifications' => $user->notifications()->whereNull('viewed_at')->whereNull('sent_at')->get()])->render(), $html);
    }

    public function replaceColors($html)
    {
        return str_replace(['#f29181', '#F29181'], getSetting('primary_color'), $html);
    }

    public function replaceYear($html)
    {
        return str_replace(['2018 On The Dot', date('Y') - 1 . ' On The Dot'], date('Y') . ' On The Dot', $html);
    }
}