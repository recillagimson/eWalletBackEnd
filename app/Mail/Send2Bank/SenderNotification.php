<?php

namespace App\Mail\Send2Bank;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SenderNotification extends Mailable
{
    use Queueable, SerializesModels;

    private string $accountNo;
    private string $amount;
    private string $serviceFee;
    private string $newBalance;
    private string $transactionDate;
    private string $provider;
    private string $refNo;
    private string $remittanceId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $accountNo, string $amount, string $serviceFee, string $newBalance,
                                string $transactionDate, string $provider, string $refNo, string $remittanceId)
    {
        //
        $this->accountNo = $accountNo;
        $this->amount = $amount;
        $this->serviceFee = $serviceFee;
        $this->newBalance = $newBalance;
        $this->transactionDate = $transactionDate;
        $this->provider = $provider;
        $this->refNo = $refNo;
        $this->remittanceId = $remittanceId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): SenderNotification
    {
        return $this->view('emails.send2bank.sender_notification')
            ->subject('SquidPay - Send To Bank Notification')
            ->with([
                'accountNo' => $this->accountNo,
                'amount' => $this->amount,
                'serviceFee' => $this->serviceFee,
                'newBalance' => $this->newBalance,
                'transactionDate' => $this->transactionDate,
                'provider' => $this->provider,
                'refNo' => $this->refNo,
                'remittanceId' => $this->remittanceId,
            ]);
    }
}
