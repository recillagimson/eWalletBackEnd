<?php

namespace App\Mail\TierUpgrade;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KYCNotification extends Mailable
{
    use Queueable, SerializesModels;

    private string $text;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $subject, string $text)
    {
        $this->subject = $subject;
        $this->text = $text;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.kyc.kyc_notification')
            ->subject($this->subject)
            ->with([
                'subject' => $this->subject,
                'text' => $this->text,
            ]);
    }
}
