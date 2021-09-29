<?php

namespace App\Mail\BPI;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UserUtilities\UserDetail;
use Illuminate\Contracts\Queue\ShouldQueue;

class CashInBPI extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $userDetail;
    private $newBalance;
    private $referenceNumber;
    public function __construct(UserDetail $userDetail, $newBalance, $referenceNumber)
    {
        $this->userDetail = $userDetail;
        $this->newBalance = $newBalance;
        $this->referenceNumber = $referenceNumber;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('view.name');
        return $this->view('emails.bpi.cashin_bpi')
            ->subject('SquidPay - Cash In BPI')
            ->with([
                'firstName' => $this->userDetail->first_name,
                'refNo' => $this->referenceNumber,
                'balance' => number_format($this->newBalance, 2),
                'createdAt' => Carbon::now()->setTimezone('Asia/Manila')->format('D, M d, Y h:m A')
            ]);
    }
}
