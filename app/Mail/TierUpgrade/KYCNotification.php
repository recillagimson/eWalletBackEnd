<?php

namespace App\Mail\TierUpgrade;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KYCNotification extends Mailable
{
    use Queueable, SerializesModels;

    private $text;
    private $subject;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $text)
    {
        $this->text = $text;
        $this->subject = $subject;
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
                'text' => $this->text,
            ]);
    }
}
