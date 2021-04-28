<?php

namespace App\Mail\SendMoney;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMoneyRecipientNotification extends Mailable
{
    use Queueable, SerializesModels;

    private array $fillRequest;
    private string $senderName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $fillRequest, string $senderName)
    {
        $this->amount = $fillRequest['amount'];
        $this->newBalance = $fillRequest['newBalance'];
        $this->refNo = $fillRequest['refNo'];
        $this->senderName = $senderName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.sendmoney.send_money_recipient_notification')
            ->subject('SquidPay - Send Money Notification')
            ->with([
                'amount' => $this->amount,
                'newBalance' => $this->newBalance,
                'refNo' => $this->refNo,
                'senderName' => $this->senderName
            ]);
    }
}
