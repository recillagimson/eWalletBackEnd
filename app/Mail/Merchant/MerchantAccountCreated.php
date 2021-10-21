<?php

namespace App\Mail\Merchant;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MerchantAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $firstName;
    public $password;
    public $pinCode;
    public $subject;

    public function __construct(string $subject, string $firstName, string $password, string $pinCode, string $email)
    {
        $this->firstName = $firstName;
        $this->password = $password;
        $this->pinCode = $pinCode;
        $this->subject = $subject;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.merchant.merchant_creation')->subject($this->subject)
                ->with([
                    'firstName' => $this->firstName,
                    'password' => $this->password,
                    'pinCode' => $this->pinCode,
                    'email' => $this->email,
                ]);
    }
}
