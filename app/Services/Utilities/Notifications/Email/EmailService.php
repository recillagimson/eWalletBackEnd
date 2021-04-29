<?php


namespace App\Services\Utilities\Notifications\Email;


use App\Enums\OtpTypes;
use App\Mail\Auth\AccountVerification;
use App\Mail\Auth\PasswordRecoveryEmail;
use App\Mail\LoginVerification;
use App\Mail\SendMoney\SendMoneyNotification;
use App\Mail\SendMoney\SendMoneyRecipientNotification;
use App\Mail\SendMoney\SendMoneySenderNotification;
use App\Mail\SendMoney\SendMoneyVerification;
use Illuminate\Http\Response;
use Illuminate\Mail\Mailable;
use Illuminate\Validation\ValidationException;
use SendGrid;
use SendGrid\Mail\Mail;

class EmailService implements IEmailService
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

    /**
     * Sends an email for password recovery verifications
     *
     * @param string $to
     * @param string $otp
     * @param string $otpType
     */
    public function sendPasswordVerification(string $to, string $otp, string $otpType)
    {
        $pinOrPassword = $otpType == OtpTypes::passwordRecovery ? 'password' : 'pin code';
        $subject = 'SquidPay - Account ' . ucwords($pinOrPassword) . ' Recovery Verification';
        $template = new PasswordRecoveryEmail($otp, $otpType);
        $this->sendMessage($to, $subject, $template);
    }

    /**
     * Sends an email for account verification and
     * activation
     *
     * @param string $to
     * @param string $otp
     */
    public function sendAccountVerification(string $to, string $otp)
    {
        $subject = 'SquidPay - Account Verification';
        $template = new AccountVerification($otp);
        $this->sendMessage($to, $subject, $template);
    }

    /**
     * Sends an email for login verification
     *
     * @param string $to
     * @param string $otp
     */
    public function sendLoginVerification(string $to, string $otp)
    {
        $subject = 'SquidPay - Login Verification';
        $template = new LoginVerification($otp);
        $this->sendMessage($to, $subject, $template);
    }

    /**
     * Sends an email for send money verification
     *
     * @param string $to
     * @param string $otp
     */
    public function sendMoneyVerification(string $to, string $otp)
    {
        $subject = 'SquidPay - Send Money Verification';
        $template = new SendMoneyVerification($otp);
        $this->sendMessage($to, $subject, $template);
    }

    /**
     * Sends an email for sender 
     *
     * @param string $to
     * @param string $amount
     * @param string $sender
     */
    public function sendMoneySenderNotification(string $to, array $fillRequest, string $receiverName)
    {
        $subject = 'SquidPay - Send Money Notification';
        $template = new SendMoneySenderNotification($fillRequest, $receiverName);
        $this->sendMessage($to, $subject, $template);
    }

    /**
     * Sends an email for recipient
     *
     * @param string $to
     * @param string $amount
     * @param string $sender
     */
    public function sendMoneyRecipientNotification(string $to, array $fillRequest, string $senderName)
    {
        $subject = 'SquidPay - Send Money Notification';
        $template = new SendMoneyRecipientNotification($fillRequest, $senderName);
        $this->sendMessage($to, $subject, $template);
    }

    private function sendMessage(string $to, string $subject, Mailable $template): void
    {
        $mail = new Mail();
        $mail->setFrom($this->fromAddress, $this->fromName);
        $mail->setSubject($subject);
        $mail->addTo($to);
        $mail->addContent('text/html', ($template)->render());

        $sendgrid = new SendGrid($this->apiKey);
        $response = $sendgrid->send($mail);

        if (!$response->statusCode() == Response::HTTP_OK) $this->sendingFailed();
    }

    function sendingFailed()
    {
        throw ValidationException::withMessages([
            'email' => 'Email provider failed to send the message. Please try again.'
        ]);
    }
}
