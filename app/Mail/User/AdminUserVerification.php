<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminUserVerification extends Mailable
{
    use Queueable, SerializesModels;

    private string $firstName;
    private string $email;
    private string $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $firstName, string $email, string $password)
    {
        //
        $this->firstName = $firstName;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): AdminUserVerification
    {
        return $this->view('emails.users.admin_user_verification')
            ->subject('SquidPay - Admin Account Details')
            ->with([
                'firstName' => $this->firstName,
                'email' => $this->email,
                'password' => $this->password,
            ]);
    }
}
