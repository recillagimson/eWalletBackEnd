<?php


namespace App\Services\Utilities\Notifications\Email;

use App\Enums\EmailSubjects;
use App\Enums\OtpTypes;
use App\Mail\Auth\AccountVerification;
use App\Mail\Auth\PasswordRecoveryEmail;
use App\Mail\BPI\CashInBPI;
use App\Mail\BuyLoad\SenderNotification as BuyLoadSenderNotification;
use App\Mail\BuyLoad\SmartPromoNotification;
use App\Mail\Cebuana\CebuanaConfirmationMail;
use App\Mail\EcPay\SuccessPayment;
use App\Mail\Farmers\BatchUploadNotification;
use App\Mail\Loan\LoanRefNumber;
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
use App\Mail\TierUpgrade\UpgradeToSilverNotification;
use App\Mail\User\AdminUserVerification;
use App\Mail\User\OtpVerification;
use App\Mail\UserTransactionMail\UserTransactionHistoryMail;
use App\Models\OutSend2Bank;
use App\Models\Tier;
use App\Models\UserAccount;
use App\Models\UserUtilities\UserDetail;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Traits\StringHelpers;
use App\Traits\Transactions\Send2BankHelpers;
use Carbon\Carbon;
use Exception;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PDF;
use SendGrid;
use SendGrid\Mail\Mail;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EmailService implements IEmailService
{
    use Send2BankHelpers, StringHelpers;

    private string $fromAddress;
    private string $fromName;
    private string $apiKey;
    private IUserAccountRepository $userAccounts;

    public function __construct(IUserAccountRepository $userAccounts)
    {
        $this->fromAddress = config('mail.from.address');
        $this->fromName = config('mail.from.name');
        $this->apiKey = config('mail.mailers.sendgrid.apiKey');
        $this->userAccounts = $userAccounts;
    }

    /**
     * Sends an email for password recovery verifications
     *
     * @param string $to
     * @param string $otp
     * @param string $otpType
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
        $template = new LoginVerification($otp, $recipientName);
        $this->sendMessage($to, $subject, $template);
    }

    /**
     * Sends an email for send money verification
     *
     * @param string $to
     * @param string $otp
     */
    public function sendMoneyVerification(string $to, string $otp, string $recipientName)
    {
        $subject = 'SquidPay - Send Money Verification';
        $user = $this->getUser();
        $firstName = $user->profile ? ucwords($user->profile->first_name) : 'Squidee';
        $template = new SendMoneyVerification($otp, $recipientName, $firstName);
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

        $user = $this->getUser();
        $firstName = $user->profile ? ucwords($user->profile->first_name) : 'Squidee';

        $template = new SendMoneySenderNotification($fillRequest, $receiverName, $firstName);
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
        $strAmount = $this->formatAmount($amount);
        $strServiceFee = $this->formatAmount($serviceFee);
        $strNewBalance = $this->formatAmount($newBalance);
        $strDate = $this->formatDate($transactionDate);
        $strProvider = $this->getSend2BankProviderCaption($provider);

        $user = $this->getUser();
        $firstName = $user->profile ? ucwords($user->profile->first_name) : 'Squidee';

        $subject = 'SquidPay - Send To Bank Notification';
        $template = new SenderNotification($hideAccountNo, $strAmount, $strServiceFee, $strNewBalance, $strDate,
            $strProvider, $refNo, $remittanceId, $firstName);

        $this->sendMessage($to, $subject, $template);
    }

    public function sendSend2BankReceipt(string $to, OutSend2Bank $send2Bank)
    {
        $subject = 'SquidPay - Send to Bank Transaction Receipt';
        $template = new Send2BankReceipt($send2Bank);
        $this->sendMessage($to, $subject, $template);
    }

    public function buyLoadNotification(string $to, float $amount, string $productName, string $recipientMobileNumber,
                                        Carbon $transactionDate, float $newBalance, string $refNo, string $recipientName)
    {
        $subject = 'SquidPay - Buy Load Notification';
        $strAmount = $this->formatAmount($amount);
        $strBalance = $this->formatAmount($newBalance);
        $strTransactionDate = $this->formatDate($transactionDate);

        $template = new BuyLoadSenderNotification($strAmount, $productName, $recipientMobileNumber, $strTransactionDate,
            $strBalance, $refNo, $recipientName);
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

    public function sendMessage(string $to, string $subject, Mailable $template, $file = null, $fileName = null): void
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

        if (!$response->statusCode() == ResponseAlias::HTTP_OK) $this->sendingFailed();
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

    public function sendEcPaySuccessPaymentNotification(string $to, UserDetail $userDetail, $newBalance, string $referenceNumber, Carbon $transactionDate)
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

    public function sendUserTransactionHistory(string $to, array $records, string $fileName, string $firstName, string $from, string $dateTo, string $password)
    {
        $pdf = PDF::loadView('reports.transaction_history.transaction_history_v2', [
            'records' => $records
        ]);
        $pdf->SetProtection(['copy', 'print'], $password, 'squidP@y');

        $subject = "User Transaction History";

        $template = new UserTransactionHistoryMail($subject, $records, $fileName, $firstName, Carbon::parse($from)->format('F d, Y'), Carbon::parse($dateTo)->format('F d, Y'), Carbon::now()->format('m/d/Y'));
        $this->sendMessage($to, $subject, $template, $pdf->output(), $fileName);
    }

    public function sendCebuanaConfirmation(string $to, string $fullName, string $firstName, string $accountNumber, string $transactionDateTime, string $addMoneyPartnerReferenceNumber, string $amount, string $referenceNumber) {
        $subject = "Confirmation Message - Squidpay Cash In";
        $template = new CebuanaConfirmationMail($firstName, $fullName, $accountNumber, $transactionDateTime, $addMoneyPartnerReferenceNumber, $amount, $referenceNumber);
        $this->sendMessage($to, $subject, $template);
    }

    public function sendSmartPromoNotification(string $to, string $firstName, float $amount, string $productName, string $refNo)
    {
        $subject = "Squidpay - Promo Notification";
        $template = new SmartPromoNotification($firstName, $amount, $productName, $refNo);
        $this->sendMessage($to, $subject, $template);
    }
}
