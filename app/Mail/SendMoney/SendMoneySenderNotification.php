<?php

namespace App\Mail\SendMoney;

use App\Traits\StringHelpers;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMoneySenderNotification extends Mailable
{
    use Queueable, SerializesModels, StringHelpers;

    private array $fillRequest;
    private string $amount;
    private string $serviceFee;
    private string $newBalance;
    private string $refNo;
    private string $receiverName;
    private string $transactionDate;
    private string $senderName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $fillRequest, string $receiverName, string $senderName)
    {
        $this->amount = $this->formatAmount($fillRequest['amount']);
        $this->serviceFee = $this->formatAmount($fillRequest['serviceFee']);
        $this->newBalance = $this->formatAmount($fillRequest['newBalance']);
        $this->refNo = $fillRequest['refNo'];
        $this->transactionDate = $this->formatDate(Carbon::now());
        $this->receiverName = ucwords($receiverName);
        $this->senderName = ucwords($senderName);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): SendMoneySenderNotification
    {
        return $this->view('emails.sendmoney.send_money_sender_notification')
            ->subject('SquidPay - Send Money Notification')
            ->with([
                'amount' => $this->amount,
                'serviceFee' => $this->serviceFee,
                'newBalance' => $this->newBalance,
                'refNo' => $this->refNo,
                'receiverName' => $this->receiverName,
                'transactionDate' => $this->transactionDate,
                'senderName' => $this->senderName
            ]);
    }
}
