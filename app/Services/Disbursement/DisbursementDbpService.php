<?php

namespace App\Services\Disbursement;

use App\Enums\DisbursementConfig;
use App\Enums\ReferenceNumberTypes;
use App\Enums\TransactionStatuses;
use App\Models\OutDisbursementDbp;
use App\Models\User;
use App\Models\UserAccount;
use App\Repositories\Disbursement\IOutDisbursementDbpRepository;
use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Repositories\Notification\INotificationRepository;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\Disbursement\IDisbursementDbpService;
use App\Services\ThirdParty\BayadCenter\IBayadCenterService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithPayBillsErrors;
use App\Traits\Errors\WithTpaErrors;
use DB;
use Exception;

class DisbursementDbpService implements IDisbursementDbpService
{
    use WithTpaErrors, WithPayBillsErrors;

    private IBayadCenterService $bayadCenterService;
    private IUserDetailRepository $userDetailRepository;
    private IReferenceNumberService $referenceNumberService;
    private IUserBalanceInfoRepository $userBalanceInfo;
    private IServiceFeeRepository $serviceFeeRepository;
    private IUserAccountRepository $userAccountRepository;
    private IUserTransactionHistoryRepository $transactionHistories;
    private INotificationService $notificationService;
    private ILogHistoryRepository $logHistory;
    private IEmailService $emailService;
    private ISmsService $smsService;
    private INotificationRepository $notificationRepository;
    private IOutDisbursementDbpRepository $outDisbursementDbpRepository;

    public function __construct(IBayadCenterService $bayadCenterService, IUserDetailRepository $userDetailRepository, IReferenceNumberService $referenceNumberService, IUserBalanceInfoRepository $userBalanceInfo, IServiceFeeRepository $serviceFeeRepository, IUserAccountRepository $userAccountRepository, IOutPayBillsRepository $outPayBillsRepository, IUserTransactionHistoryRepository $transactionHistories, INotificationService $notificationService, ILogHistoryRepository $logHistory, IEmailService $emailService, ISmsService $smsService, INotificationRepository $notificationRepository, IOutDisbursementDbpRepository $outDisbursementDbpRepository)
    {
        $this->bayadCenterService = $bayadCenterService;
        $this->userDetailRepository = $userDetailRepository;
        $this->referenceNumberService = $referenceNumberService;
        $this->userBalanceInfo = $userBalanceInfo;
        $this->serviceFeeRepository = $serviceFeeRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->outPayBillsRepository = $outPayBillsRepository;
        $this->transactionHistories = $transactionHistories;
        $this->notificationService = $notificationService;
        $this->logHistory = $logHistory;
        $this->emailService = $emailService;
        $this->smsService = $smsService;
        $this->notificationRepository = $notificationRepository;
        $this->outDisbursementDbpRepository = $outDisbursementDbpRepository;
    }



    public function transaction(UserAccount $user,$fillRequest): array
    {
        $clientUser = $this->getClientUser($fillRequest);
        if (!$clientUser) $this->invalidUser();
        $isEnough = $this->checkAmount($clientUser, $fillRequest);
        if (!$isEnough) $this->insuficientBalance();

        DB::beginTransaction();
        try {
            $this->subtractBalance($clientUser, $fillRequest);
            $outDisbursementDbp = $this->outDisbursementDbp($user, $clientUser, $fillRequest);
            $this->logHistories($user, $clientUser, $outDisbursementDbp);
            $this->userTransactionHistory($user, $clientUser, $outDisbursementDbp);

            DB::commit();
            return [$outDisbursementDbp];
        } catch (Exception $e) {
            DB::rollBack();
        }
      
    }




    // Private Methods 

    private function checkAmount(UserAccount $user, $fillRequest)
    {
        $balance = $this->userBalanceInfo->getUserBalance($user->id);
        if ($balance >= $fillRequest['amount']) return true;
    }


    private function getClientUser($fillRequest)
    {
        return $this->userAccountRepository->getUserByAccountNumberAndRsbsaNumber($fillRequest['account_number'], $fillRequest['rsbsa_number']);
    }

    private function subtractBalance(UserAccount $user, $fillRequest)
    {
        $balance = $this->userBalanceInfo->getUserBalance($user->id);
        $newBalance = $balance - $fillRequest['amount'];
        $this->userBalanceInfo->updateUserBalance($user->id, $newBalance);
    }

    private function getReference()
    {
        return $this->referenceNumberService->generate(ReferenceNumberTypes::DI);
    }

    private function outDisbursementDbp(UserAccount $user, UserAccount $clientUser,$fillRequest)
    {
        return $this->outDisbursementDbpRepository->create([
            'user_account_id' => $clientUser->account_number,
            'reference_number' => $this->getReference(),
            'total_amount' => $fillRequest['amount'],
            'status' => TransactionStatuses::success,
            'transaction_date' => date('Y-m-d H:i:s'),
            'transaction_category_id' => DisbursementConfig::DI,
            'transaction_remarks' => 'Cash Disbursement',
            'disbursed_by' => $user->id,
            'user_created' => $user->id,
            'user_updated' => ''
        ]);
    }

    private function logHistories(UserAccount $user, UserAccount $clientUser, $outDisbursementDbp)
    {
        $this->logHistory->create([
            'user_account_id' => $clientUser->id,
            'reference_number' => $outDisbursementDbp->reference_number,
            'squidpay_module' => 'Disbursement',
            'namespace' => 'DI',
            'transaction_date' => $outDisbursementDbp->transaction_date,
            'remarks' => 'Cash Disbursement',
            'operation' => 'Add',
            'user_created' => $user->id,
            'user_updated' => ''
        ]);
    }

    private function userTransactionHistory(UserAccount $user, UserAccount $clientUser, $outDisbursementDbp)
    {
        $this->transactionHistories->create([
            'user_account_id' => $clientUser->id,
            'transaction_id' => $outDisbursementDbp->id,
            'reference_number' => $outDisbursementDbp->reference_number,
            'total_amount' => $outDisbursementDbp->total_amount,
            'transaction_category_id' => DisbursementConfig::DI,
            'user_created' => $user->id,
            'transaction_date' => $outDisbursementDbp->transaction_date
        ]);
    }

}
