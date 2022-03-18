<?php


namespace App\Services\Utilities\Notifications\SMS;


use App\Enums\OtpTypes;
use App\Models\Tier;
use App\Models\UserAccount;
use App\Models\UserUtilities\UserDetail;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Utilities\API\IApiService;
use App\Traits\StringHelpers;
use App\Traits\Transactions\Send2BankHelpers;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SmsService implements ISmsService
{
    use Send2BankHelpers, StringHelpers;

    protected string $apiUrl;

    protected string $username;

    protected string $password;

    protected string $shotcodeMask;

    private IApiService $apiService;
    private string $broadcastUrl;
    private IUserAccountRepository $userAccounts;

    public function __construct(IApiService $apiService, IUserAccountRepository $userAccounts)
    {
        $this->apiUrl = config('sms.api_url');
        $this->username = config('sms.username');
        $this->password = config('sms.password');
        $this->shotcodeMask = config('sms.shortcode_mask');

        $this->apiService = $apiService;
        $this->broadcastUrl = $this->apiUrl . '/broadcast';
        $this->userAccounts = $userAccounts;
    }

    public function sendPasswordVerification(string $to, string $otp, string $otpType, string $recipientName)
    {

        $pinOrPassword = $otpType === OtpTypes::passwordRecovery ? 'password' : 'pin code';

        $content = 'Hi ' . $recipientName . '! Your ' . $pinOrPassword . ' recovery code is: ' . $otp . '. PLEASE DO NOT SHARE YOUR OTP.';
        $this->sendMessages($to, $content);
    }

    public function sendAccountVerification(string $to, string $otp, string $recipientName)
    {

        $content = 'Hi ' . $recipientName . '! Your account verification code is: ' . $otp . '. PLEASE DO NOT SHARE YOUR OTP.';
        $this->sendMessages($to, $content);
    }

    public function sendLoginVerification(string $to, string $otp, string $recipientName)
    {
        $content = 'Hi ' . $recipientName . '! Your login verification code is: ' . $otp . '. PLEASE DO NOT SHARE YOUR OTP.';
        $this->sendMessages($to, $content);
    }

    public function sendMoneyVerification(string $to, string $otp, string $recipientName)
    {
        $content = 'Hi ' . $recipientName . '! Your send money verification code is: ' . $otp . '. PLEASE DO NOT SHARE YOUR OTP.';
        $this->sendMessages($to, $content);
    }

    public function sendS2BVerification(string $to, string $otp, string $recipientName)
    {
        $content = 'Hi ' . $recipientName . '! Your send to bank verification code is: ' . $otp . '. PLEASE DO NOT SHARE YOUR OTP.';
        $this->sendMessages($to, $content);
    }

    public function updateProfileVerification(string $to, string $otp, string $recipientName)
    {
        $content = 'Hi ' . $recipientName . '! Your update profile verification code is: ' . $otp . '. PLEASE DO NOT SHARE YOUR OTP.';
        $this->sendMessages($to, $content);
    }

    public function updateMobileVerification(string $to, string $otp, string $recipientName)
    {

        $content = 'Hi ' . $recipientName . '! Your update mobile verification code is: ' . $otp . '. PLEASE DO NOT SHARE YOUR OTP.';
        $this->sendMessages($to, $content);
    }

    public function sendMoneySenderNotification(string $to, array $fillRequest, string $receiverName)
    {
        $strAmount = $this->formatAmount($fillRequest['amount']);
        $strNewBalance = $this->formatAmount($fillRequest['newBalance']);

        $user = $this->getUser();
        $firstName = $user->profile ? ucwords($user->profile->first_name) : 'Squidee';

        $content = 'Hi ' . $firstName . '! You have forwarded: ' . $strAmount . ' to ' . $receiverName .
            '. This amount has been debited to your account. Your new balance is P ' . $strNewBalance .
            ' with Ref No. ' . $fillRequest['refNo'] . '. Thank you for using SquidPay!';
        $this->sendMessages($to, $content);
    }

    public function sendMoneyRecipientNotification(string $to, array $fillRequest, string $senderName)
    {
        $strAmount = $this->formatAmount($fillRequest['amount']);
        $strNewBalance = $this->formatAmount($fillRequest['newBalance']);
        $strDate = $this->formatDate(Carbon::now());

        $firstName = ucwords($fillRequest['receiverName']);


        $content = 'Hi ' . $firstName . '! You have received P' . $strAmount . ' of SquidPay on ' . $strDate .
            ' from ' . $senderName . '. Your new balance is P' . $strNewBalance . ' with Ref No. ' . $fillRequest['refNo'] .
            '. Use now to buy load, send money, pay bills and a lot more!';
        $this->sendMessages($to, $content);
    }

    public function payBillsNotification(string $to, array $fillRequest, string $biller)
    {
        $strAmount = $this->formatAmount($fillRequest['amount']);
        $strServiceFee = $this->formatAmount($fillRequest['serviceFee']);
        $strDate = $this->formatDate(Carbon::now());

        $user = $this->getUser();
        $firstName = $user->profile ? ucwords($user->profile->first_name) : 'Squidee';

        $content = 'Hi ' . $firstName . '! Your payment of P' . $strAmount . ' to ' . $fillRequest['biller'] .
            ' with fee P' . $strServiceFee . '. has been successfully processed on ' . $strDate .
            ' with Ref No. ' . $fillRequest['refNo'] . '. Visit https://my.squid.ph/ for more information or contact support@squid.ph.';
        $this->sendMessages($to, $content);
    }

    public function sendSend2BankSenderNotification(string $to, string $refNo, string $accountNo, float $amount,
                                                    Carbon $transactionDate, float $serviceFee, float $newBalance, string $provider, string $remittanceId)
    {
        $hideAccountNo = Str::substr($accountNo, 0, -4);
        $strAmount = $this->formatAmount($amount);
        $strServiceFee = $this->formatAmount($serviceFee);
        $strNewBalance = $this->formatAmount($newBalance);
        $strDate = $this->formatDate($transactionDate);
        $strProvider = $this->getSend2BankProviderCaption($provider);

        $user = $this->getUser();
        $firstName = $user->profile ? ucwords($user->profile->first_name) : 'Squidee';

        $content = 'Hi ' . $firstName . '! You have sent P' . $strAmount . ' of SquidPay on ' . $strDate . ' to the account ending in '
            . $hideAccountNo . '. Service Fee for this transaction is P' . $strServiceFee . '. Your new balance is P'
            . $strNewBalance . ' with SquidPay Ref. No. ' . $refNo . ' & ' . $strProvider . ' Remittance No. ' . $remittanceId
            . '. Thank you for using SquidPay!';
        $this->sendMessages($to, $content);
    }

    public function buyLoadNotification(string $to, float $amount, string $productName, string $recipientMobileNumber,
                                        Carbon $transactionDate, float $newBalance, string $refNo, string $recipientName)
    {
        $strDate = $this->formatDate($transactionDate);
        $strAmount = $this->formatAmount($amount);
        $strNewBalance = $this->formatAmount($newBalance);

        $content = 'Hi ' . $recipientName . '! You have paid P' . $strAmount . ' of SquidPay to purchase ' . $productName . ' for ' .
            $recipientMobileNumber . ' on ' . $strDate . '. Your SquidPay balance is P' . $strNewBalance .
            '. Ref. No. ' . $refNo . '.';
        $this->sendMessages($to, $content);
    }

    public function sendMessages(string $to, string $content)
    {
        $message = $this->buildMessage($to, $content);
        $response = $this->apiService->post($this->broadcastUrl, $message);
        if (!$response->successful()) $this->sendingFailed();
    }

    private function buildMessage(string $to, string $content): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'msisdn' => $to,
            'content' => $content,
            'shortcode_mask' => $this->shotcodeMask,
        ];
    }

    private function sendingFailed()
    {
        throw ValidationException::withMessages([
            'sms' => 'SMS provider failed to send the message. Please try again.'
        ]);
    }

    public function tierUpgradeNotification(string $to, UserDetail $userDetail, Tier $tier)
    {
        $content = "Hi " . $userDetail->first_name . ", Your account is now fully verified. Login to your account to enjoy additional features. Thank you.";
        $this->sendMessages($to, $content);
    }

    public function kycNotification(string $to, string $message)
    {
        $this->sendMessages($to, $message);
    }

    private function getUser(): UserAccount
    {
        $userId = request()->user()->id;
        return $this->userAccounts->getUser($userId);
    }

    public function sendLoanConfirmation(string $to, string $firstName, string $refNo)
    {
        $content = "Hi " . $firstName. ", Thank you for using Squidpay My Loan. Please take note of your Squidpay Loan Application ID: " . $refNo . ". Kindly enter this in our loan partner, RFSC application page. For inquiries and concerns, you may call our 24/7 Customer Support Hotline at (632) 8521-7035 or email us at support@squidpay.ph.";
        $this->sendMessages($to, $content);
    }

    public function sendBPICashInNotification(string $to, UserDetail $userDetail, $newBalance, string $referenceNumber)
    {
        $date = Carbon::now()->setTimezone('Asia/Manila')->format('D, M d, Y h:m A');
        $content = "Hi " . $userDetail->first_name . "! You have successfully added funds to your wallet via BPI on " . $date . " . Service fee for this transaction is P 10.00. Your new balance is P " . number_format($newBalance, 2) . " with reference no. " . $referenceNumber . ". Thank you for using SquidPay!";
        $this->sendMessages($to, $content);
    }

    public function sendEcPaySuccessPaymentNotification(string $to, UserDetail $userDetail, $newBalance, string $referenceNumber, Carbon $transactionDate)
    {
        $date = $transactionDate->setTimezone('Asia/Manila')->format('D, M d, Y h:m A');
        $content = "Hi " . $userDetail->first_name . "! You have successfully added funds to your wallet via EC Pay on " .
            $date . " . Service fee for this transaction is P 0.00. Your new balance is P " . number_format($newBalance, 2) .
            " with reference no. " . $referenceNumber . ". Thank you for using SquidPay!" ;
        $this->sendMessages($to, $content);
    }

    public function sendMerchantAccoutCredentials(string $to, string $firstName, string $password, string $pinCode) {
        $content = 'Hi ' . $firstName . '! You merchant account has been created. Please see below details: Username: ' . $to . ', Password: ' . $password . ', PIN: ' . $pinCode . '. Thank you!';
        $this->sendMessages($to, $content);
    }

    public function sendUserTransactionHistory(string $to, array $records, string $fileName, string $firstName, string $from, string $dateTo, string $password) {

    }

    public function sendCebuanaConfirmation(string $to, string $fullName, string $firstName, string $accountNumber, string $transactionDateTime, string $addMoneyPartnerReferenceNumber, string $amount, string $referenceNumber) {
        $content = "Thank you for using Squidpay Add Money. You have successfully added " . $amount . " to your Squidpay wallet on " . $transactionDateTime ." with Reference Number " . $referenceNumber . ".";
        $this->sendMessages($to, $content);
    }

    public function sendSmartPromoNotification(string $to, string $firstName, float $amount, string $productName, string $refNo)
    {
        $content = 'Hi ' . $firstName . '! Thank you for purchasing ' . $productName . '. We have successfully credited the amount Php' . $amount .
            ' to your account as part of the SquidPay Libreng Credits to Smart Promo. Promo period from 2021-12-16 to 2022-03-15. ' .
            'Ref. No. ' . $refNo . '. DTI Fair Trade Permit No. FTEB-133969 Series of 2021';
        $this->sendMessages($to, $content);
    }

}
