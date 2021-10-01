<?php

namespace App\Services\AddmoneyCebuana;

use App\Models\InAddMoneyCebuana;
use App\Enums\ReferenceNumberTypes;
use App\Enums\TransactionStatuses;
use App\Enums\SquidPayModuleTypes;
use App\Enums\TransactionCategories;
use App\Enums\TransactionCategoryIds;
use App\Repositories\Repository;
use App\Services\Transaction\ITransactionValidationService;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\Errors\WithTpaErrors;
use App\Traits\Errors\WithTransactionErrors;
use App\Traits\Errors\WithUserErrors;
use App\Traits\StringHelpers;
use App\Traits\Transactions\Send2BankHelpers;
use App\Traits\UserHelpers;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddMoneyCebuanaService extends Repository implements IAddMoneyCebuanaService
{
    use WithAuthErrors, WithUserErrors, WithTpaErrors, WithTransactionErrors;
    use UserHelpers, Send2BankHelpers, StringHelpers;

    private IReferenceNumberService $referenceNumberService;
    private ITransactionValidationService $transactionValidationService;
    private IUserAccountRepository $users;
    private IServiceFeeRepository $serviceFees;
    private ILogHistoryService $logHistoryService;

    public function __construct(InAddMoneyCebuana $model,
                                ITransactionValidationService $transactionValidationService,
                                IServiceFeeRepository $serviceFees,
                                IReferenceNumberService $referenceNumberService,
                                ILogHistoryService $logHistoryService,
                                IUserAccountRepository $users)
    {
        parent::__construct($model);
        $this->transactionValidationService = $transactionValidationService;
        $this->users = $users;
        $this->serviceFees = $serviceFees;
        $this->referenceNumberService = $referenceNumberService;
        $this->logHistoryService = $logHistoryService;
    }

    public function addMoney($userId, array $data)
    {
        return $this->validateFundTransfer($userId, $data);
    }

    public function validateFundTransfer($userId, $data)
    {
        $user = $this->users->getUser($userId);
        if (!$user) $this->userAccountNotFound();
        //$this->transactionValidationService->validateUser($user);

        $serviceFee = $this->serviceFees->getByTierAndTransCategory($user->tier_id,
            TransactionCategoryIds::addMoneyCebuana);
            
        $serviceFeeAmount = $serviceFee ? $serviceFee->amount : 0;
        $serviceFeeId = $serviceFee ? $serviceFee->id : '';
        $totalAmount = $data['amount'] + $serviceFeeAmount;
        
        $this->transactionValidationService->validate($user,
            TransactionCategoryIds::addMoneyCebuana, $totalAmount);
        $refNo = $this->referenceNumberService->generate(ReferenceNumberTypes::AddMoneyCebuana);

        try {
            DB::beginTransaction();
            $response = $this->createTransaction($user, $refNo, $data['amount'], $serviceFeeAmount,$serviceFeeId, 
                $totalAmount, TransactionCategoryIds::addMoneyCebuana);
            DB::commit();
            return $response;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Add Money Error: ', $e->getTrace());
        }

    }

    private function createTransaction($user, string $refNo, float $amount, float $serviceFee,
                                       string $serviceFeeId, float $totalAmount, string $transactionCategoryID)
    {
        $data = [
            'user_account_id' => $user->id,
            'reference_number' => $refNo,
            'amount' => $amount,
            'service_fee' => $serviceFee,
            'service_fee_id' => $serviceFeeId,
            'total_amount' => $totalAmount,
            'transaction_date' => Carbon::now()->toDateTimeString(),
            'expiration_date' => Carbon::now()->addDays(1)->toDateTimeString(),
            'transaction_category_id' => $transactionCategoryID,
            'transaction_remarks' => 'Add Money via Cebuana',
            'status' => TransactionStatuses::pending,
            'cebuana_reference' => $this->referenceNumberService->generate(ReferenceNumberTypes::AddMoneyCebuana),
            'posted_date' => Carbon::now()->toDateTimeString(),
            'user_created' => $user->id,
            'user_updated' => $user->id,
        ];

        return $this->model->create($data);
    }

}
