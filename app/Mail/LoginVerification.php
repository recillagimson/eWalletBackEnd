<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginVerification extends Mailable
{
    use Queueable, SerializesModels;

    private string $otp;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $otp)
    {
        //
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.auth.login_verification')
            ->subject('SquidPay - Login Verification')
            ->with([
                'code' => $this->otp
            ]);
    }
}
