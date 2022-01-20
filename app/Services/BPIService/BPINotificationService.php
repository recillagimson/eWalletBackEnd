<?php

namespace App\Services\BPIService;

use App\Mail\BPI\ChineseNewYearPromo;
use App\Models\UserAccount;
use App\Services\BPIService\Models\BPIPromoNotifcation;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\SMS\ISmsService;

class BPINotificationService implements IBPINotificationService
{
    private ISmsService $smsService;
    private IEmailService $emailService;

    public function __construct(ISmsService $smsService, IEmailService $emailService)
    {
        $this->smsService = $smsService;
        $this->emailService = $emailService;
    }

    public function sendPromoSms(string $mobileNumber, BPIPromoNotifcation $params)
    {
        $strAmount = number_format($params->amount, 2);
        $content = "Hi $params->firstName! Thank you for your cash-in using BPI. We have successfully credited the amount" .
            "of Php $strAmount to your account as part of the BPI Chinese New Year Promo with SquidPay. Promo Period " .
            "2022-01-05 to 2022-02-28. Ref. No. $params->refNo. DTI Fair Trade Permit No. FTEB-135259 Series of 2022.";

        $this->smsService->sendMessages($mobileNumber, $content);
    }

    public function sendPromoEmail(string $email, BPIPromoNotifcation $params)
    {
        $subject = 'SquidPay - Cash-in Promo Notification';
        $template = new ChineseNewYearPromo($params);
        $this->emailService->sendMessage($email, $subject, $template);
    }



}
