<?php

namespace App\Mail\Farmers;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BatchUploadNotification extends Mailable
{
    use Queueable, SerializesModels;

    private string $firstName;
    private string $successLink;
    private string $failedLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $firstName, string $successLink, string $failedLink)
    {
        //
        $this->firstName = $firstName;
        $this->successLink = $successLink;
        $this->failedLink = $failedLink;
        $this->subject = "SquidPay - Farmers Batch Upload Notification";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): BatchUploadNotification
    {
        return $this->view('emails.farmers.batch_upload_notif')
            ->subject($this->subject)
            ->with([
                'firstName' => $this->firstName,
                'successLink' => $this->successLink,
                'failedLink' => $this->failedLink
            ]);
    }
}
