<?php

namespace App\Mail\SendMoney;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMoneyVerification extends Mailable
{
    use Queueable, SerializesModels;

    private string $otp;
    private string $recipientName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $otp, string $recipientName)
    {
        $this->otp = $otp;
        $this->recipientName = $recipientName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): SendMoneyVerification
    {
        return $this->view('emails.sendmoney.send_money_verification')
            ->subject('SquidPay - Send Money Verification')
            ->with([
                'code' => $this->otp,
                'recipientName' => $this->recipientName
            ]);
    }
}
