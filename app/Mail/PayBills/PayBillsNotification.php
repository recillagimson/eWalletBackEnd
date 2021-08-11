<?php

namespace App\Mail\PayBills;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayBillsNotification extends Mailable
{
    use Queueable, SerializesModels;

    private array $fillRequest;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $fillRequest)
    {
        $this->amount = $fillRequest['amount'];
        $this->serviceFee = $fillRequest['serviceFee'];
        $this->newBalance = $fillRequest['newBalance'];
        $this->refNo = $fillRequest['refNo'];
        $this->biller = $fillRequest['biller'];
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
            'biller' => $this->biller
        ]);
    }
}
