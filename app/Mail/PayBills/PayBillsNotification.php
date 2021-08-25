<?php

namespace App\Mail\PayBills;

use App\Traits\StringHelpers;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayBillsNotification extends Mailable
{
    use Queueable, SerializesModels, StringHelpers;

    private array $fillRequest;
    private string $amount;
    private string $serviceFee;
    private string $newBalance;
    private string $refNo;
    private string $biller;
    private string $transactionDate;
    private string $firstName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $fillRequest, string $firstName)
    {
        $this->amount = $this->formatAmount($fillRequest['amount']);
        $this->serviceFee = $this->formatAmount($fillRequest['serviceFee']);
        $this->newBalance = $this->formatAmount($fillRequest['newBalance']);
        $this->refNo = $fillRequest['refNo'];
        $this->biller = $fillRequest['biller'];
        $this->transactionDate = $this->formatDate(Carbon::now());
        $this->firstName = $firstName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.paybills.pay_bills_notification')
            ->subject('SquidPay - Pay Bills Notification')
            ->with([
                'amount' => $this->amount,
                'serviceFee' => $this->serviceFee,
                'newBalance' => $this->newBalance,
                'refNo' => $this->refNo,
                'biller' => $this->biller,
                'transactionDate' => $this->transactionDate,
                'firstName' => $this->firstName
            ]);
    }
}
