<?php

namespace App\Mail\BuyLoad;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SenderNotification extends Mailable
{
    use Queueable, SerializesModels;

    private string $amount;
    private string $productName;
    private string $recipientMobileNumber;
    private string $transactionDate;
    private string $newBalance;
    private string $refNo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $amount, string $productName, string $recipientMobileNumber,
                                string $transactionDate, string $newBalance, string $refNo)
    {
        //
        $this->amount = $amount;
        $this->productName = $productName;
        $this->recipientMobileNumber = $recipientMobileNumber;
        $this->transactionDate = $transactionDate;
        $this->newBalance = $newBalance;
        $this->refNo = $refNo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): SenderNotification
    {
        return $this->view('emails.buyload.sender_notification')
            ->subject('SquidPay - Buy Load Notification')
            ->with([
                'amount' => $this->amount,
                'productName' => $this->productName,
                'recipientMobileNumber' => $this->recipientMobileNumber,
                'transactionDate' => $this->transactionDate,
                'newBalance' => $this->newBalance,
                'refNo' => $this->refNo,
            ]);
    }
}
