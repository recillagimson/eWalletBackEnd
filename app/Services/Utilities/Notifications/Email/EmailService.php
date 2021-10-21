<?php


namespace App\Services\Utilities\Notifications\Email;

use App\Enums\EmailSubjects;
use App\Enums\OtpTypes;
use App\Mail\Auth\AccountVerification;
use App\Mail\Auth\PasswordRecoveryEmail;
use App\Mail\BuyLoad\SenderNotification as BuyLoadSenderNotification;
use App\Mail\EcPay\SuccessPayment;
use App\Mail\LoginVerification;
use App\Mail\Merchant\MerchantAccountCreated;
use App\Mail\PayBills\PayBillsNotification;
use App\Mail\Send2Bank\Send2BankReceipt;
use App\Mail\Send2Bank\SenderNotification;
use App\Mail\SendMoney\SendMoneyRecipientNotification;
use App\Mail\SendMoney\SendMoneySenderNotification;
use App\Mail\SendMoney\SendMoneyVerification;
use App\Mail\TierApproval\TierUpgradeRequestApproved;
use App\Mail\TierUpgrade\KYCNotification;
use App\Mail\User\AdminUserVerification;
use App\Mail\User\OtpVerification;
use App\Mail\UserTransactionMail\UserTransactionHistoryMail;
use App\Models\OutSend2Bank;
use App\Models\Tier;
use App\Models\UserAccount;
use App\Models\UserUtilities\UserDetail;
use App\Traits\Transactions\Send2BankHelpers;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
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
     * @param string $recipientName
     */
    public function sendPasswordVerification(string $to, string $otp, string $otpType, string $recipientName)
    {
        $pinOrPassword = $otpType == OtpTypes::passwordRecovery ? 'password' : 'pin code';
        $subject = 'SquidPay - Account ' . ucwords($pinOrPassword) . ' Recovery Verification';
        $template = new PasswordRecoveryEmail($otp, $otpType, $recipientName);
        $this->sendMessage($to, $subject, $template);
    }

    /**
     * Sends an email for account verification and
     * activation
     *
     * @param string $to
     * @param string $otp
     * @param string $recipientName
     */
    public function sendAccountVerification(string $to, string $otp, string $recipientName)
    {
        $subject = 'SquidPay - Account Verification';
        $template = new AccountVerification($otp, $recipientName);
        $this->sendMessage($to, $subject, $template);
    }

    /**
     * Sends an email for login verification
     *
     * @param string $to
     * @param string $otp
     * @param string $recipientName
     */
    public function sendLoginVerification(string $to, string $otp, string $recipientName)
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
     * @param string $recipientName
     */
    public function sendMoneyVerification(string $to, string $otp, string $recipientName)
    {
        $subject = 'SquidPay - Send Money Verification';
        $template = new SendMoneyVerification($otp);
        $this->sendMessage($to, $subject, $template);
    }

    public function sendS2BVerification(string $to, string $otp, string $recipientName)
    {
        $subject = 'SquidPay - Send to Bank Verification';
        $template = new OtpVerification($subject, $otp, $recipientName);
        $this->sendMessage($to, $subject, $template);
    }

    /**
     * Sends an email for update email verification
     *
     * @param string $to
     * @param string $otp
     * @param string $recipientName
     */
    public function updateEmailVerification(string $to, string $otp, string $recipientName)
    {
        $subject = 'SquidPay - Update Email Verification';
        $template = new OtpVerification($subject, $otp, $recipientName);
        $this->sendMessage($to, $subject, $template);
    }

    /**
     * Sends an email for update profile verification
     *
     * @param string $to
     * @param string $otp
     * @param string $recipientName
     */
    public function updateProfileVerification(string $to, string $otp, string $recipientName)
    {
        $subject = 'SquidPay - Update Profile Verification';
        $template = new OtpVerification($subject, $otp, $recipientName);
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
        $strBalance = number_format($newBalance, 2);
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
        $template = new PayBillsNotification($fillRequest, $biller);
        $this->sendMessage($to, $subject, $template);
    }

    private function sendMessage(string $to, string $subject, Mailable $template, $file = null, $fileName = null): void
    {
        $mail = new Mail();
        $mail->setFrom($this->fromAddress, $this->fromName);
        $mail->setSubject($subject);
        $mail->addTo($to);
        $mail->addContent('text/html', ($template)->render());

        if($file && $fileName) {
            // $mail->attachData($file, $fileName);
            $mail->addAttachment(
                $file,
                "application/pdf",
                $fileName,
                "attachment"
            );
        }

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

    public function batchUploadNotification(UserAccount $user, string $successLink, string $failedLink)
    {
        $subject = EmailSubjects::farmersBatchUploadNotif;
        $firstName = ucwords($user->profile->first_name);
        $template = new BatchUploadNotification($firstName, $successLink, $failedLink);
    }

    private function getUser(): UserAccount
    {
        $userId = request()->user()->id;
        return $this->userAccounts->getUser($userId);
    }

    public function kycNotification(UserAccount $user, string $text)
    {
        $subject = EmailSubjects::kycNotification;
        $to = $user->email;
        $template = new KYCNotification($subject, $text);

        $this->sendMessage($to, $subject, $template);
    }

    public function sendLoanReferenceNumber(string $firstName, string $refNo, string $to) {
        $subject = 'SquidPay - Loan Confirmation';
        $template = new LoanRefNumber($firstName, $refNo);
        $this->sendMessage($to, $subject, $template);
    }

    public function sendBPICashInNotification(string $to, UserDetail $userDetail, $newBalance, string $referenceNumber)
    {
        $subject = "Cash In via BPI";
        $template = new CashInBPI($userDetail, $newBalance, $referenceNumber);
        $this->sendMessage($to, $subject, $template);
    }

    public function sendEcPaySuccessPaymentNotification(string $to, UserDetail $userDetail, $newBalance, string $referenceNumber)
    {
        $subject = "Payment via EcPay";
        $template = new SuccessPayment($userDetail, $newBalance, $referenceNumber);
        $this->sendMessage($to, $subject, $template);
    }

    public function sendMerchantAccoutCredentials(string $to, string $firstName, string $password, string $pinCode) {
        $subject = "Merchant Account Created";
        $template = new MerchantAccountCreated($subject, $firstName, $password, $pinCode, $to);
        $this->sendMessage($to, $subject, $template);
    }

    public function sendUserTransactionHistory(string $to, array $records, string $fileName, string $firstName, string $from, string $dateTo, string $password) {
        $records = [];
        foreach($records as $item) {
            $entry = $item;
            $record = [
                $entry['manila_time_transaction_date'],
                $entry['name'],
                $entry['reference_number'],
                $entry['transaction_type'] == 'DR' ? $entry['total_amount'] : '',
                $entry['transaction_type'] == 'CR' ? $entry['total_amount'] : '',
                $entry['available_balance']
            ];
            array_push($records, $record);
        }

        $pdf = PDF::loadView('reports.transaction_history.transaction_history_v2', [
            'records' => $records
        ]);
        $pdf->SetProtection(['copy', 'print'], $password, 'squidP@y');

        $subject = "User Transaction History";

        $template = new UserTransactionHistoryMail($subject, $records, $fileName, $firstName, Carbon::parse($from)->format('F d, Y'), Carbon::parse($dateTo)->format('F d, Y'));
        $this->sendMessage($to, $subject, $template, $pdf->output(), $fileName);
    }
}
