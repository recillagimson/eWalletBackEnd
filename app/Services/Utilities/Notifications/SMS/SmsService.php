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

        $content = 'Hi ' . $recipientName . '! Your ' . $pinOrPassword . ' recovery code is: ' . $otp . '. PLEASE DO NOT SHARE YOUR OTP';
        $this->sendMessages($to, $content);
    }

    public function sendAccountVerification(string $to, string $otp, string $recipientName)
    {

        $content = 'Hi ' . $recipientName . '! Your account verification code is: ' . $otp . '. PLEASE DO NOT SHARE YOUR OTP';
        $this->sendMessages($to, $content);
    }

    public function sendLoginVerification(string $to, string $otp, string $recipientName)
    {
        $content = 'Hi ' . $recipientName . '! Your login verification code is: ' . $otp . '. PLEASE DO NOT SHARE YOUR OTP';
        $this->sendMessages($to, $content);
    }

    public function sendMoneyVerification(string $to, string $otp, string $recipientName)
    {
        $content = 'Hi ' . $recipientName . '! Your send money verification code is: ' . $otp . '. PLEASE DO NOT SHARE YOUR OTP';
        $this->sendMessages($to, $content);
    }

    public function sendS2BVerification(string $to, string $otp, string $recipientName)
    {
        $content = 'Hi ' . $recipientName . '! Your send to bank verification code is: ' . $otp . '. PLEASE DO NOT SHARE YOUR OTP';
        $this->sendMessages($to, $content);
    }

    public function updateProfileVerification(string $to, string $otp, string $recipientName)
    {
        $content = 'Hi ' . $recipientName . '! Your update profile verification code is: ' . $otp . '. PLEASE DO NOT SHARE YOUR OTP';
        $this->sendMessages($to, $content);
    }

    public function updateMobileVerification(string $to, string $otp, string $recipientName)
    {

        $content = 'Hi ' . $recipientName . '! Your update mobile verification code is: ' . $otp . '. PLEASE DO NOT SHARE YOUR OTP';
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
                                        Carbon $transactionDate, float $newBalance, string $refNo)
    {
        $strDate = $this->formatDate($transactionDate);
        $strAmount = $this->formatAmount($amount);
        $strNewBalance = $this->formatAmount($newBalance);

        $user = $this->getUser();
        $firstName = $user->profile ? ucwords($user->profile->first_name) : 'Squidee';

        $content = 'Hi ' . $firstName . '! You have paid P' . $strAmount . ' of SquidPay to purchase ' . $productName . ' for ' .
            $recipientMobileNumber . ' on ' . $strDate . '. Your SquidPay balance is P' . $strNewBalance .
            '. Ref. No. ' . $refNo . '.';
        $this->sendMessages($to, $content);
    }

    private function sendMessages(string $to, string $content)
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
        $content = "Hi " . $userDetail->first_name . "! You have successfully added funds to your wallet via BPI on " . $date . " . Service fee for this transaction is PHP 0.00. Your new balance is " . $newBalance . " with reference no. " . $referenceNumber . ".";
        $this->sendMessages($to, $content);
    }

}
