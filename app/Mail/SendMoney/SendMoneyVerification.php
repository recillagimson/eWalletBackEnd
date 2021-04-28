<?php

namespace App\Mail\SendMoney;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMoneyVerification extends Mailable
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
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.sendmoney.send_money_verification')
        ->subject('SquidPay - Send Money Verification')
        ->with([
            'code' => $this->otp
        ]);
    }
}
