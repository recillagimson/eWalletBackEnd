<?php

namespace App\Mail\Send2Bank;

use App\Models\OutSend2Bank;
use App\Traits\StringHelpers;
use App\Traits\Transactions\Send2BankHelpers;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Send2BankReceipt extends Mailable
{
    use Queueable, SerializesModels;
    use Send2BankHelpers, StringHelpers;

    private OutSend2Bank $send2Bank;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(OutSend2Bank $send2Bank)
    {
        $this->send2Bank = $send2Bank;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): Send2BankReceipt
    {
        $amount = $this->formatAmount($this->send2Bank->amount);
        $serviceFee = $this->formatAmount($this->send2Bank->service_fee);
        $transactionDate = $this->formatDate($this->send2Bank->transaction_date);
        $provider = $this->getSend2BankProviderCaption($this->send2Bank->provider);

        return $this->view('emails.send2bank.receipt')
            ->subject('SquidPay - Send to Bank Transaction Receipt')
            ->with([
                'amount' => $amount,
                'accountName' => $this->send2Bank->account_name,
                'accountNumber' => $this->send2Bank->account_number,
                'serviceFee' => $serviceFee,
                'transactionDate' => $transactionDate,
                'refNo' => $this->send2Bank->reference_number,
                'provider' => $provider,
                'remittanceId' => $this->send2Bank->provider_remittance_id
            ]);
    }
}
