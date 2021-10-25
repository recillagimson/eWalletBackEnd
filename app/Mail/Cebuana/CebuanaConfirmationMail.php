<?php

namespace App\Mail\Cebuana;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CebuanaConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $fullName;
    public $accountNumber;
    public $transactionDateTime;
    public $addMoneyPartnerReferenceNumber;
    public $firstName;
    public $amount;
    public $referenceNumber;

    public function __construct(string $firstName, string $fullName, string $accountNumber, string $transactionDateTime, string $addMoneyPartnerReferenceNumber, string $amount, string $referenceNumber)
    {
        $this->fullName = $fullName;
        $this->accountNumber = $accountNumber;
        $this->transactionDateTime = $transactionDateTime;
        $this->addMoneyPartnerReferenceNumber = $addMoneyPartnerReferenceNumber;
        $this->firstName = $firstName;
        $this->amount = $amount;
        $this->referenceNumber = $referenceNumber;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.add_money.cebuana_cashin', [
            'fullName' => $this->fullName,
            'accountNumber' => $this->accountNumber,
            'transactionDateTime' => $this->transactionDateTime,
            'addMoneyPartnerReferenceNumber' => $this->addMoneyPartnerReferenceNumber,
            'firstName' => $this->firstName,
            'amount' => $this->amount,
            'referenceNumber' => $this->referenceNumber,
        ]);
    }
}
