<?php

namespace App\Mail\SendMoney;

use App\Traits\StringHelpers;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMoneyRecipientNotification extends Mailable
{
    use Queueable, SerializesModels, StringHelpers;

    private array $fillRequest;
    private string $senderName;
    private string $receiverName;
    private string $transactionDate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $fillRequest, string $senderName)
    {
        $this->amount = $this->formatAmount($fillRequest['amount']);
        $this->newBalance = $this->formatAmount($fillRequest['newBalance']);
        $this->refNo = $fillRequest['refNo'];
        $this->senderName = ucwords($senderName);
        $this->receiverName = ucwords($fillRequest['receiverName']);
        $this->transactionDate = $this->formatDate(Carbon::now());

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
                'senderName' => $this->senderName,
                'receiverName' => $this->receiverName,
                'transactionDate' => $this->transactionDate
            ]);
    }
}
