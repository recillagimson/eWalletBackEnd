<?php


namespace App\Services\Utilities\Notifications\SMS;


use App\Enums\OtpTypes;
use App\Models\Tier;
use App\Models\UserUtilities\UserDetail;
use App\Services\Utilities\API\IApiService;
use App\Traits\Transactions\Send2BankHelpers;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SmsService implements ISmsService
{
    use Send2BankHelpers;

    protected string $apiUrl;

    protected string $username;

    protected string $password;

    protected string $shotcodeMask;

    private IApiService $apiService;
    private string $broadcastUrl;

    public function __construct(IApiService $apiService)
    {
        $this->apiUrl = config('sms.api_url');
        $this->username = config('sms.username');
        $this->password = config('sms.password');
        $this->shotcodeMask = config('sms.shortcode_mask');

        $this->apiService = $apiService;
        $this->broadcastUrl = $this->apiUrl.'/broadcast';
    }

    public function sendPasswordVerification(string $to, string $otp, string $otpType)
    {
        $pinOrPassword = $otpType === OtpTypes::passwordRecovery ? 'password' : 'pin code';
        $content = 'Hi Squidee! Your ' . $pinOrPassword . ' recovery code is: ' . $otp . '. DO NOT SHARE this OTP';
        $this->sendMessages($to, $content);
    }

    public function sendAccountVerification(string $to, string $otp)
    {
        $content = 'Hi Squidee! Your account verification code is: '.$otp .'. DO NOT SHARE this OTP';
        $this->sendMessages($to, $content);
    }

    public function sendLoginVerification(string $to, string $otp)
    {
        $content = 'Hi Squidee! Your login verification code is: ' . $otp . '. DO NOT SHARE this OTP';
        $this->sendMessages($to, $content);
    }

    public function sendMoneyVerification(string $to, string $otp)
    {
        $content = 'Hi Squidee! Your send money verification code is: ' . $otp . '. DO NOT SHARE this OTP';
        $this->sendMessages($to, $content);
    }

    public function sendS2BVerification(string $to, string $otp)
    {
        $content = 'Hi Squidee! Your send to bank verification code is: ' . $otp . '. DO NOT SHARE this OTP';
        $this->sendMessages($to, $content);
    }

    public function updateProfileVerification(string $to, string $otp)
    {
        $content = 'Hi Squidee! Your update profile verification code is: ' . $otp . '. DO NOT SHARE this OTP';
        $this->sendMessages($to, $content);
    }

    public function updateMobileVerification(string $to, string $otp)
    {
        $content = 'Hi Squidee! Your update mobile verification code is: ' . $otp . '. DO NOT SHARE this OTP';
        $this->sendMessages($to, $content);
    }

    public function sendMoneySenderNotification(string $to, array $fillRequest, string $receiverName)
    {
        $content = 'Hi Squidee! You have forwarded: ' . $fillRequest['amount'] . ' to ' . $receiverName . '. This amount has been debited to your account. Your new balance is P ' . $fillRequest['newBalance'] . ' with Ref No. ' . $fillRequest['refNo'] . '. Thank you for using SquidPay!';
        $this->sendMessages($to, $content);
    }


    public function sendMoneyRecipientNotification(string $to, array $fillRequest, string $senderName)
    {
        $content = 'Hi Squidee! You have received P' . $fillRequest['amount'] . ' of SquidPay on ' . date('Y-m-d H:i:s') . ' from ' . $senderName . '. Your new balance is P' . $fillRequest['newBalance'] . ' with Ref No. ' . $fillRequest['refNo'] . '. Use now to buy load, send money, pay bills and a lot more!';
        $this->sendMessages($to, $content);
    }

    public function payBillsNotification(string $to, array $fillRequest, string $biller)
    {
        $content = 'Hi Squidee! You have paid P' . $fillRequest['amount'] . ' of SquidPay on ' . date('Y-m-d H:i:s') . ' to ' . $biller . '. Your new balance is P' . $fillRequest['newBalance'] . ' with Ref No. ' . $fillRequest['refNo'] . '. Thank you for using our Pay Bills service.';
        $this->sendMessages($to, $content);
    }

    public function sendSend2BankSenderNotification(string $to, string $refNo, string $accountNo, float $amount,
                                                    Carbon $transactionDate, float $serviceFee, float $newBalance, string $provider, string $remittanceId)
    {
        $hideAccountNo = Str::substr($accountNo, 0, -4);
        $strAmount = number_format($amount, 2, '.', ',');
        $strServiceFee = number_format($serviceFee, 2, '.', ',');
        $strNewBalance = number_format($newBalance, 2, '.', ',');
        $strDate = $transactionDate->toDayDateTimeString();
        $strProvider = $this->getSend2BankProviderCaption($provider);

        $content = 'Hi Squidee! You have sent P' . $strAmount . ' of SquidPay on ' . $strDate . ' to the account ending in '
            . $hideAccountNo . '. Service Fee for this transaction is P' . $strServiceFee . '. Your new balance is P'
            . $strNewBalance . ' with SquidPay Ref. No. ' . $refNo . ' & ' . $strProvider . ' Remittance No. ' . $remittanceId
            . '. Thank you for using SquidPay!';
        $this->sendMessages($to, $content);
    }

    public function buyLoadNotification(string $to, float $amount, string $productName, string $recipientMobileNumber,
                                        Carbon $transactionDate, float $newBalance, string $refNo)
    {
        $strDate = $transactionDate->toDayDateTimeString();
        $strAmount = number_format($amount, 2);
        $strNewBalance = number_format($newBalance, 2);

        $content = 'Hi Squidee! You have paid P' . $strAmount . ' of SquidPay to purchase ' . $productName . ' for ' .
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

    public function tierUpgradeNotification(string $to, UserDetail $userDetail, Tier $tier) {
       $content = "Hi Squidee! Your tier upgrade has been approved. Your tier is now Silver with 100k transaction limit, you may now enjoy Send Money and Send to Bank features";
        $this->sendMessages($to, $content);
    }


}
