<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpVerification extends Mailable
{
    use Queueable, SerializesModels;

    private string $code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $subject, string $code)
    {
        $this->code = $code;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): OtpVerification
    {
        return $this->view('emails.users.otp_verification')
            ->subject($this->subject)
            ->with([
                'subject' => $this->subject,
                'code' => $this->code,
            ]);
    }
}
