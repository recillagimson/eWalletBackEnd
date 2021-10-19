<?php

namespace App\Mail\UserTransactionMail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
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

    public function __construct(string $subject, array $records)
    {
        $this->subject = $subject;    
        $this->records = $records;    
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fileName = '11111111.pdf';
        $file = Excel::download(new UserTransactionHistoryExport(new Collection($this->records)), $fileName);
        dd($file);
        return $this->view('view.name');
    }
}
