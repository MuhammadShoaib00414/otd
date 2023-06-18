<?php

namespace App\Console\Commands;

use App\User;
use App\Helpers\EmailHelper;
use App\EmailNotification;
use Illuminate\Console\Command;
use App\Option; // Replace with your Option model
use App\Mail\PendingApprovalNotification; // Replace with your notification class
 
class SendPendingApprovalNotification extends Command
{
    protected $signature = 'notification:send-pending-approval';

    protected $description = 'Send notifications for pending options awaiting approval';

    public function handle(EmailHelper $helper)
    {
        $pendingOptionsCount = Option::where('is_enabled', 0)->count();
   
        if ($pendingOptionsCount > 0) {
            $users = User::where('is_admin', 1)->get();
    
            foreach ($users as $user) {
                $emailNotification = EmailNotification::find(20);
                // $user->notify(new PendingApprovalNotification($user));
                $locale = $user->locale;
    
                $html = $helper->replaceTagsForUser($emailNotification->htmlWithLocale($locale), $user);
                $html = $helper->replaceCtaWith($html, config('app.url').'/notifications');
                $html = $helper->replaceNotificationsWithFeed($html, $user);
                $html = $helper->replaceSystemTags($html);
               
                
                $this->sendEmail($user, $emailNotification, $locale, $html);
            }
        }
    
        $this->info('Pending approval notifications sent successfully.');
    }
    

    private function sendEmail($user, $emailNotification, $locale, $html)
    {
        $this->output->writeln('Sending notification to: '.$user->email);
        
        \Mail::send([], [], function ($message) use ($user, $emailNotification, $locale, $html) {
            $message->from(config('mail.from.address'), getsetting('from_email_name', $locale))
                    ->to($user->email)
                    ->subject($emailNotification->subjectWithLocale($locale))
                    ->setBody($html, 'text/html');
        });
    }
    
}
