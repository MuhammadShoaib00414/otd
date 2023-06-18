<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\EmailNotification;
use App\Helpers\EmailHelper;

class Receipt extends Mailable
{
    use Queueable, SerializesModels;

    public $receipt;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $receipt)
    {
        $this->user = $user;
        $this->receipt = $receipt;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(EmailHelper $helper)
    {
        $emailNotification = EmailNotification::where('name', 'Payment Confirmation')->first();
        $locale = $this->user->locale;

        if($emailNotification->is_enabled && $this->user->deleted_at === null) {
            $url = config('app.url') . '/purchases';
            $html = $helper->replaceCtaWith($emailNotification->email_html, $url);
            $receiptView = view('components.receipts.show')->with(['receipt' => $this->receipt, 'isSimple' => false, 'showGroups' => false])->render();
            $html = $helper->replaceReceiptWith($html, $receiptView);
            $html = $helper->replaceSystemTags($html);
            
            return $this->from(config('mail.from.address'), getsetting('from_email_name', $locale))
                        ->subject($emailNotification->subjectWithLocale($locale))
                        ->html($html);
        }
    }
}
