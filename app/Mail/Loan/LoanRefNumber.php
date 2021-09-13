<?php

namespace App\Mail\Loan;

use App\Models\UserAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoanRefNumber extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $firstName;
    private $refNo;

    public function __construct(string $firstName, string $refNo)
    {
        $this->firstName = $firstName;
        $this->refNo = $refNo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('emails.tier_approval.tier_upgrade_request_approved');
        return $this->view('emails.loan.loan_ref')
            ->subject('SquidPay - Loan Confirmation')
            ->with([
                'firstName' => $this->firstName,
                'refNo' => $this->refNo
            ]);
    }
}
