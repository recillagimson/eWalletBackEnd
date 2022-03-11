<?php

namespace App\Mail\EcPay;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UserUtilities\UserDetail;
use Carbon\Carbon;

class SuccessPayment extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private UserDetail $userDetail;
    private float $newBalance;
    private string $referenceNumber;
    private Carbon $transactionDate;

    public function __construct(UserDetail $userDetail, float $newBalance, string $referenceNumber, Carbon $transactionDate)
    {
        $this->userDetail = $userDetail;
        $this->newBalance = $newBalance;
        $this->referenceNumber = $referenceNumber;
        $this->transactionDate = $transactionDate;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): SuccessPayment
    {
        return $this->view('emails.ecpay.success_payment')->subject('SquidPay - Payment via EcPay')
                ->with([
                    'firstName' => $this->userDetail->first_name,
                    'referenceNumber' => $this->referenceNumber,
                    'newBalance' => number_format($this->newBalance, 2),
                    'createdAt' => $this->transactionDate->setTimezone('Asia/Manila')->format('D, M d, Y h:m A')
                ]);
    }
}
