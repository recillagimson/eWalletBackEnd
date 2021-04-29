<?php

namespace App\Mail\SendMoney;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMoneySenderNotification extends Mailable
{
    use Queueable, SerializesModels;

    private array $fillRequest;
    private string $receiverName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $fillRequest, string $receiverName)
    {
        $this->amount = $fillRequest['amount'];
        $this->serviceFee = $fillRequest['serviceFee'];
        $this->newBalance = $fillRequest['newBalance'];
        $this->refNo = $fillRequest['refNo'];
        $this->receiverName = $receiverName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.sendmoney.send_money_sender_notification')
            ->subject('SquidPay - Send Money Notification')
            ->with([
                'amount' => $this->amount,
                'serviceFee' => $this->serviceFee,
                'newBalance' => $this->newBalance,
                'refNo' => $this->refNo,
                'receiverName' => $this->receiverName
            ]);
    }
}
