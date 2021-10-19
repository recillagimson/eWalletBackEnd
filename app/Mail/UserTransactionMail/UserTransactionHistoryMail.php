<?php

namespace App\Mail\UserTransactionMail;

use PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use App\Exports\TransactionReport\TransactionReport;
use App\Exports\UserTransaction\UserTransactionHistoryExport;

class UserTransactionHistoryMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $subject;
    public $records;
    public $fileName;
    public $firstName;
    public $from;
    public $to;

    public function __construct(string $subject, array $records, string $fileName, string $firstName, string $from, $to)
    {
        $this->subject = $subject;    
        $this->records = $records;
        $this->fileName = $fileName;
        $this->firstName = $firstName;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        return $this->view('emails.transaction_history.transaction_history', [
            'firstName' => $this->firstName,
            'from' => $this->from,
            'to' => $this->to
        ]);
    }
}
