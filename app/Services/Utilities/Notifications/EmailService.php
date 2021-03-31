<?php


namespace App\Services\Utilities\Notifications;


use App\Mail\Auth\PasswordRecoveryEmail;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use SendGrid;
use SendGrid\Mail\Mail;

class EmailService implements INotificationService
{
    private string $fromAddress;
    private string $fromName;
    private string $apiKey;

    public function __construct()
    {
        $this->fromAddress = config('mail.from.address');
        $this->fromName = config('mail.from.name');
        $this->apiKey = config('mail.mailers.sendgrid.apiKey');
    }

    public function sendPasswordVerification(string $to, string $otp)
    {
        $mail =  new Mail();
        $mail->setFrom($this->fromAddress, $this->fromName);
        $mail->setSubject('SquidPay - Account Password Recovery Verification');
        $mail->addTo($to);
        $mail->addContent('text/html', (new PasswordRecoveryEmail($otp))->render());

        $sendgrid = new SendGrid($this->apiKey);
        $response = $sendgrid->send($mail);

        if(!$response->statusCode() == Response::HTTP_OK) $this->sendingFailed();
    }

    private function sendingFailed()
    {
        throw ValidationException::withMessages([
            'email' => 'Email provider failed to send the message. Please try again.'
        ]);
    }
}
