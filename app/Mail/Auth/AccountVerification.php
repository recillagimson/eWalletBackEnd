<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountVerification extends Mailable
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
    public function build(): AccountVerification
    {
        return $this->view('emails.auth.account_verification')
            ->subject('SquidPay - Account Verification')
            ->with([
                'code' => $this->otp
            ]);
    }
}
