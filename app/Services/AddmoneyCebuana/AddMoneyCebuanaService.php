<?php

namespace App\Services\AddmoneyCebuana;

use App\Enums\Cebuana;
use Exception;
use Carbon\Carbon;
use App\Traits\UserHelpers;
use Illuminate\Support\Str;
use App\Traits\StringHelpers;
use App\Repositories\Repository;
use App\Models\InAddMoneyCebuana;
use App\Enums\SquidPayModuleTypes;
use App\Enums\TransactionStatuses;
use Illuminate\Support\Facades\DB;
use App\Enums\ReferenceNumberTypes;
use Illuminate\Support\Facades\Log;
use App\Enums\TransactionCategories;
use App\Traits\Errors\WithTpaErrors;
use App\Enums\TransactionCategoryIds;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\Errors\WithUserErrors;
use App\Traits\Errors\WithTransactionErrors;
use App\Traits\Transactions\Send2BankHelpers;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Repositories\InAddMoneyCebuana\IInAddMoneyCebuanaRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use PhpParser\Node\Expr\Cast\Double;

class AddMoneyCebuanaService extends Repository implements IAddMoneyCebuanaService
{
    use WithAuthErrors, WithUserErrors, WithTpaErrors, WithTransactionErrors;
    use UserHelpers, Send2BankHelpers, StringHelpers;

    private IReferenceNumberService $referenceNumberService;
    private ITransactionValidationService $transactionValidationService;
    private IUserAccountRepository $users;
    private IServiceFeeRepository $serviceFees;
    private ILogHistoryService $logHistoryService;
    private IInAddMoneyCebuanaRepository $addMoneyCebuanaRepository;
    private IServiceFeeRepository $serviceFeeRepository;
    private IEmailService $emailService;
    private ISmsService $smsService;
    private IUserBalanceInfoRepository $userBalanceInfo;
    private IUserAccountRepository $userAccountRepository;

    public function __construct(InAddMoneyCebuana $model,
                                ITransactionValidationService $transactionValidationService,
                                IServiceFeeRepository $serviceFees,
                                IReferenceNumberService $referenceNumberService,
                                ILogHistoryService $logHistoryService,
                                IUserAccountRepository $users,
                                IInAddMoneyCebuanaRepository $addMoneyCebuanaRepository,
                                IServiceFeeRepository $serviceFeeRepository,
                                IEmailService $emailService,
                                ISmsService $smsService,
                                IUserBalanceInfoRepository $userBalanceInfo,
                                IUserAccountRepository $userAccountRepository
                                )
    {
        parent::__construct($model);
        $this->transactionValidationService = $transactionValidationService;
        $this->users = $users;
        $this->serviceFees = $serviceFees;
        $this->referenceNumberService = $referenceNumberService;
        $this->logHistoryService = $logHistoryService;
        $this->addMoneyCebuanaRepository = $addMoneyCebuanaRepository;
        $this->serviceFeeRepository = $serviceFeeRepository;
        $this->emailService = $emailService;
        $this->smsService = $smsService;
        $this->userBalanceInfo = $userBalanceInfo;
        $this->userAccountRepository = $userAccountRepository;
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


    // CEBUANA v2
    public function generate(string $authUser, string $tierId) {
        $count = $this->addMoneyCebuanaRepository->countRecords();
        $cebuanaReferenceNumber = Carbon::now()->format('Ymd') . Str::padLeft(($count + 1), 5, "0");
        $serviceFee = $this->serviceFeeRepository->getByTierAndTransCategory($tierId, TransactionCategoryIds::addMoneyCebuana);
        return $this->addMoneyCebuanaRepository->create(
            [
                'user_account_id' => $authUser,
                'reference_number' => $this->referenceNumberService->generate(ReferenceNumberTypes::AddMoneyCebuana),
                'amount' => 0,
                'service_fee' => 0,
                'service_fee_id' => $serviceFee ? $serviceFee->id : '',
                'total_amount' => 0,
                'transaction_date' => Carbon::now(),
                'expiration_date' => Carbon::now()->addDays(1),
                'transaction_category_id' => TransactionCategoryIds::addMoneyCebuana,
                'transaction_remarks' => 'Add Money via Cebuana',
                'status' => TransactionStatuses::pending,
                'cebuana_reference' => $cebuanaReferenceNumber,
                'posted_date' => Carbon::now(),
                'user_created' => $authUser,
                'user_updated' => $authUser,
            ]
        );
    }

    public function submit(array $attr) {
        $record = $this->addMoneyCebuanaRepository->getByReferenceNumber($attr['reference_number']);
        
        // VALIDATE IF EXIST
        if(!$record) {
            throw $this->referenceNumberNotFound();
        }

        // VALIDATE EXPIRATION
        $diffInHours = Carbon::now()->diffInHours(Carbon::parse($record->created_at));
        if($diffInHours > 24) {
            throw $this->referenceNumberExpired();
        }

        // VALIDATE MIN AND MAX AMOUNT
        $amount = (Double)$attr['amount'];
        if($amount < (Double)$attr['amount']) {
            throw $this->lowerThanMinimumAmount();
        }

        if($amount > (Double)$attr['amount']) {
            throw $this->higherThanMaximumAmount();
        }
        $userAccount = $this->userAccountRepository->get($record->user_account_id);
        $serviceFee = $this->serviceFeeRepository->getByTierAndTransCategory($userAccount->tier_id, TransactionCategoryIds::cashinBPI);
        $serviceFeeAmount = Cebuana::serviceFeeDefault;

        if((Double) $attr['amount'] >= Cebuana::serviceFeeMinForPercentage) {
            $serviceFeeAmount = (Double) $attr['amount'] * (Double) Cebuana::serviceFeePercentage;
        }

        \DB::beginTransaction();
        try {
            $this->addMoneyCebuanaRepository->update($record, [
                'amount' => $attr['amount'],
                'status' => 'SUCCESS',
                'service_fee_id' => $serviceFee ? $serviceFee->id : "",
                'service_fee' => $serviceFeeAmount
            ]);

            // UPDATE USER BALANCE
            $balance = $this->userBalanceInfo->getUserBalance($record->user_account_id);
            $newBalance = (Double) $balance + (Double) $attr['amount'];
            $this->userBalanceInfo->updateUserBalance($record->user_account_id, $newBalance);
            $balance = $this->userBalanceInfo->getUserBalance($record->user_account_id);

            $fullName = $record->user_detail->first_name . " " . $record->user_detail->last_name;
            if($record && $record->user_account && $record->user_detail && $record->user_account->email) {
                $this->emailService->sendCebuanaConfirmation($record->user_account->email, $fullName, $record->user_detail->first_name, $record->user_account->account_number, Carbon::parse($record->created_at)->format('F d, Y h:i A'), $record->cebuana_reference, $record->amount, $record->reference_number);
            } 
            
            if($record && $record->user_account && $record->user_detail && $record->user_account->mobile_number){
                $this->smsService->sendCebuanaConfirmation($record->user_account->mobile_number, $fullName, $record->user_detail->first_name, $record->user_account->account_number, Carbon::parse($record->created_at)->format('F d, Y h:i A'), $record->cebuana_reference, $record->amount, $record->reference_number);
            }
    
            \DB::commit();
            return [
                'transaction_id' => $record->id,
                'amount' => $attr['amount'],
                'status' => 'SUCCESS'
            ];
        } catch (\Exception $e) {
            \DB::rollBack();
            $this->addMoneyCebuanaRepository->update($record, [
                'amount' => $attr['amount'],
                'status' => 'FAILED'
            ]);
            \Log::info($e);
            // throw $e;
            return [
                'transaction_id' => $record->id,
                'amount' => $attr['amount'],
                'status' => 'FAILED'
            ];
        }
    }

}
