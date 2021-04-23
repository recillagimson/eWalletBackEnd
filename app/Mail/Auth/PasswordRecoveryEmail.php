<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordRecoveryEmail extends Mailable
{
    use Queueable, SerializesModels;

    private string $otp;
    private string $pinOrPassword;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $otp, string $pinOrPassword)
    {
        $this->otp = $otp;
        $this->pinOrPassword = $pinOrPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.auth.password_recovery')
            ->subject('SquidPay - Account ' . ucwords($this->pinOrPassword) . ' Recovery Verification')
            ->with([
                'code' => $this->otp,
                'pinOrPassword' => $this->pinOrPassword
            ]);
    }
}
