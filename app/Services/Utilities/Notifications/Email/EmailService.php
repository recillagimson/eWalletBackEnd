<?php


namespace App\Services\Utilities\Notifications\Email;


use App\Enums\OtpTypes;
use App\Mail\Auth\AccountVerification;
use App\Mail\Auth\PasswordRecoveryEmail;
use App\Mail\BuyLoad\SenderNotification as BuyLoadSenderNotification;
use App\Mail\LoginVerification;
use App\Mail\Send2Bank\Send2BankReceipt;
use App\Mail\Send2Bank\SenderNotification;
use App\Mail\SendMoney\SendMoneyRecipientNotification;
use App\Mail\SendMoney\SendMoneySenderNotification;
use App\Mail\SendMoney\SendMoneyVerification;
use App\Mail\TierApproval\TierUpgradeRequestApproved;
use App\Mail\User\AdminUserVerification;
use App\Mail\User\OtpVerification;
use App\Models\OutSend2Bank;
use App\Models\Tier;
use App\Models\UserUtilities\UserDetail;
use App\Traits\Transactions\Send2BankHelpers;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use SendGrid;
use SendGrid\Mail\Mail;

class EmailService implements IEmailService
{
    use Send2BankHelpers;

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
     * Sends an email for update email verification
     *
     * @param string $to
     * @param string $otp
     */
    public function updateEmailVerification(string $to, string $otp)
    {
        $subject = 'SquidPay - Update Email Verification';
        $template = new OtpVerification($subject, $otp);
        $this->sendMessage($to, $subject, $template);
    }

    /**
     * Sends an email for update profile verification
     *
     * @param string $to
     * @param string $otp
     */
    public function updateProfileVerification(string $to, string $otp)
    {
        $subject = 'SquidPay - Update Profile Verification';
        $template = new OtpVerification($subject, $otp);
        $this->sendMessage($to, $subject, $template);
    }

    /**
     * Sends an email for sender
     *
     * @param string $to
     * @param array $fillRequest
     * @param string $receiverName
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
     * @param array $fillRequest
     * @param string $senderName
     */
    public function sendMoneyRecipientNotification(string $to, array $fillRequest, string $senderName)
    {
        $subject = 'SquidPay - Send Money Notification';
        $template = new SendMoneyRecipientNotification($fillRequest, $senderName);
        $this->sendMessage($to, $subject, $template);
    }

    public function sendSend2BankSenderNotification(string $to, string $refNo, string $accountNo, float $amount,
                                                    Carbon $transactionDate, float $serviceFee, float $newBalance,
                                                    string $provider, string $remittanceId)
    {
        $hideAccountNo = Str::substr($accountNo, 0, -4);
        $strAmount = number_format($amount, 2, '.', ',');
        $strServiceFee = number_format($serviceFee, 2, '.', ',');
        $strNewBalance = number_format($newBalance, 2, '.', ',');
        $strDate = $transactionDate->toDayDateTimeString();
        $strProvider = $this->getSend2BankProviderCaption($provider);

        $subject = 'SquidPay - Send To Bank Notification';
        $template = new SenderNotification($hideAccountNo, $strAmount, $strServiceFee, $strNewBalance, $strDate,
            $strProvider, $refNo, $remittanceId);

        $this->sendMessage($to, $subject, $template);
    }

    public function sendSend2BankReceipt(string $to, OutSend2Bank $send2Bank)
    {
        $subject = 'SquidPay - Send to Bank Transaction Receipt';
        $template = new Send2BankReceipt($send2Bank);
        $this->sendMessage($to, $subject, $template);
    }

    public function buyLoadNotification(string $to, float $amount, string $productName, string $recipientMobileNumber,
                                        Carbon $transactionDate, float $newBalance, string $refNo)
    {
        $subject = 'SquidPay - Buy Load Notification';
        $strAmount = number_format($amount, 2);
        $strBalance = number_format($amount, 2);
        $strTransactionDate = $transactionDate->toDayDateTimeString();

        $template = new BuyLoadSenderNotification($strAmount, $productName, $recipientMobileNumber, $strTransactionDate,
            $strBalance, $refNo);
        $this->sendMessage($to, $subject, $template);
    }

    public function sendAdminUserAccountDetails(string $to, string $firtName, string $email, string $password)
    {
        $subject = 'SquidPay - Admin Account Details';
        $strFirstName = ucwords($firtName);

        $template = new AdminUserVerification($strFirstName, $email, $password);
        $this->sendMessage($to, $subject, $template);
    }

    public function payBillsNotification(string $to, array $fillRequest, string $biller)
    {
        $subject = 'SquidPay - Pay Bills Notification';
        $template = new SendMoneySenderNotification($fillRequest, $biller);
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


    public function tierUpgradeNotification(string $to, UserDetail $userDetail, Tier $tier)
    {
        $subject = 'SquidPay - Tier Upgrade Update';
        $template = new TierUpgradeRequestApproved($userDetail, $tier);
        $this->sendMessage($to, $subject, $template);
    }

}
