<?php


namespace App\Services\Utilities\Notifications;

use App\Models\Tier;
use App\Models\UserUtilities\UserDetail;
use Carbon\Carbon;

class NotificationService implements INotificationService
{
    public function __construct()
    {
    }


    public function sendLoginVerification(string $to, string $otp, string $recipientName)
    {
        // TODO: Implement sendLoginVerification() method.
    }

    public function sendAccountVerification(string $to, string $otp, string $recipientName)
    {
        // TODO: Implement sendAccountVerification() method.
    }

    public function sendPasswordVerification(string $to, string $otp, string $otpType, string $recipientName)
    {
        // TODO: Implement sendPasswordVerification() method.
    }

    public function sendMoneyVerification(string $to, string $otp, string $recipientName)
    {
        // TODO: Implement sendMoneyVerification() method.
    }

    public function sendS2BVerification(string $to, string $otp, string $recipientName)
    {
        // TODO: Implement sendS2BVerification() method.
    }

    public function updateProfileVerification(string $to, string $otp, string $recipientName)
    {
        // TODO: Implement updateProfileVerification() method.
    }

    public function sendMoneySenderNotification(string $to, array $fillRequest, string $receiverName)
    {
        // TODO: Implement sendMoneySenderNotification() method.
    }

    public function sendMoneyRecipientNotification(string $to, array $fillRequest, string $senderName)
    {
        // TODO: Implement sendMoneyRecipientNotification() method.
    }

    public function payBillsNotification(string $to, array $fillRequest, string $biller)
    {
        // TODO: Implement payBillsNotification() method.
    }

    public function sendSend2BankSenderNotification(string $to, string $refNo, string $accountNo, float $amount, Carbon $transactionDate, float $serviceFee, float $newBalance, string $provider, string $remittanceId)
    {
        // TODO: Implement sendSend2BankSenderNotification() method.
    }

    public function buyLoadNotification(string $to, float $amount, string $productName, string $recipientMobileNumber, Carbon $transactionDate, float $newBalance, string $refNo,
                                        string $recipientName)
    {
        // TODO: Implement buyLoadNotification() method.
    }

    public function tierUpgradeNotification(string $to, UserDetail $userDetail, Tier $tier)
    {
        // TODO: Implement tierUpgradeNotification() method.
    }

    public function sendBPICashInNotification(string $to, UserDetail $userDetail, $newBalance, string $referenceNumber)
    {
        // TODO: Implement sendBPICashInNotification() method.
    }

    public function sendMerchantAccoutCredentials(string $to, string $firstName, string $password, string $pinCode) {
        // TODO: Implement sendMerchantAccoutCredentials() method.
    }

    public function sendUserTransactionHistory(string $to, array $records, string $fileName, string $firstName, string $from, string $dateTo, string $password){
        // TODO: Implement sendUserTransactionHistory() method.
    }

    public function sendCebuanaConfirmation(string $to, string $fullName, string $firstName, string $accountNumber, string $transactionDateTime, string $addMoneyPartnerReferenceNumber, string $amount, string $referenceNumber) {
        // TODO: Implement sendCebuanaConfirmation() method.
    }

    public function sendSmartPromoNotification(string $to, string $firstName, float $amount, string $productName, string $refNo)
    {
        // TODO: Implement sendSmartPromoNotification() method.
    }

    public function sendEcPaySuccessPaymentNotification(string $to, UserDetail $userDetail, $newBalance, string $referenceNumber, Carbon $transactionDate)
    {
        // TODO: Implement sendEcPaySuccessPaymentNotification() method.
    }
}
