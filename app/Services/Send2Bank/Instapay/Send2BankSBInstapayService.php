<?php


namespace App\Services\Send2Bank\Instapay;


use App\Enums\TpaProviders;
use App\Enums\TransactionCategoryIds;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Send2Bank\Send2BankService;
use App\Services\ThirdParty\SecurityBank\ISecurityBankService;
use App\Services\ThirdParty\UBP\IUBPService;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\OTP\IOtpService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;

class Send2BankSBInstapayService extends Send2BankService implements ISend2BankSBInstapayService
{

    public function __construct(IUBPService $ubpService,
                                ISecurityBankService $securityBankService,
                                IReferenceNumberService $referenceNumberService,
                                ITransactionValidationService $transactionValidationService,
                                INotificationService $notificationService,
                                ISmsService $smsService,
                                IEmailService $emailService,
                                IOtpService $otpService,
                                ILogHistoryService $logHistoryService,
                                IUserAccountRepository $users,
                                IUserBalanceInfoRepository $userBalances,
                                IOutSend2BankRepository $send2banks,
                                IServiceFeeRepository $serviceFees,
                                IUserTransactionHistoryRepository $transactionHistories)
    {
        parent::__construct($ubpService, $securityBankService, $referenceNumberService, $transactionValidationService, $notificationService,
            $smsService, $emailService, $otpService, $users, $userBalances, $send2banks, $serviceFees, $transactionHistories, $logHistoryService);

        $this->transactionCategoryId = TransactionCategoryIds::send2BankInstaPay;
        $this->provider = TpaProviders::secBankInstapay;
    }

}
