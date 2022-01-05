<?php

namespace App\Mail\BuyLoad;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SmartPromoNotification extends Mailable
{
    use Queueable, SerializesModels;

    private string $firstname;
    private string $amount;
    private string $productName;
    private string $refNo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $firstname, float $amount, string $productName, string $refNo)
    {
        //
        $this->firstname = $firstname;
        $this->amount = number_format($amount, 2);
        $this->productName = $productName;
        $this->refNo = $refNo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): SmartPromoNotification
    {
        return $this->view('emails.buyload.smart_promo_notification')
            ->subject('SquidPay - Promo Notification')
            ->with([
                'firstName' => $this->firstname,
                'amount' => $this->amount,
                'productName' => $this->productName,
                'refNo' => $this->refNo
            ]);
    }
}
