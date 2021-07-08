<?php

namespace App\Services\Send2BankSecBank;

use App\Enums\OtpTypes;
use App\Enums\TpaProviders;
use App\Models\OutSend2Bank;
use Illuminate\Support\Carbon;
use App\Enums\SquidPayModuleTypes;
use App\Enums\TransactionStatuses;
use App\Enums\ReferenceNumberTypes;
use App\Enums\TransactionCategoryIds;
use App\Services\Utilities\OTP\IOtpService;
use App\Traits\Errors\WithTransactionErrors;
use App\Traits\Transactions\Send2BankHelpers;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\SecurityBank\IPesoNetBankRepository;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\ThirdParty\SecurityBank\ISecurityBankService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;

class PesoNetService implements IPesoNetService
{   

    use WithTransactionErrors, Send2BankHelpers;

    private IUserAccountRepository $users;
    private IServiceFeeRepository $serviceFees;
    private ITransactionValidationService $transactionValidationService;
    private ISecurityBankService $securityBankService;
    private IReferenceNumberService $referenceNumberService;
    private IOtpService $otpService;
    private IPesoNetBankRepository $pesoNetBank;
    private IOutSend2BankRepository $send2banks;
    private ILogHistoryService $logHistoryService;
    private IUserTransactionHistoryRepository $transactionHistories;
    private ISmsService $smsService;
    private IEmailService $emailService;

    public function __construct(IUserAccountRepository $users, IServiceFeeRepository $serviceFees, ITransactionValidationService $transactionValidationService, ISecurityBankService $securityBankService, IReferenceNumberService $referenceNumberService, IOtpService $otpService, IPesoNetBankRepository $pesoNetBank, IOutSend2BankRepository $send2banks, ILogHistoryService $logHistoryService, IUserTransactionHistoryRepository $transactionHistories, ISmsService $smsService, IEmailService $emailService)
    {
        $this->users = $users;
        $this->serviceFees = $serviceFees;
        $this->transactionValidationService = $transactionValidationService;
        $this->securityBankService = $securityBankService;
        $this->referenceNumberService = $referenceNumberService;
        $this->otpService = $otpService;
        $this->pesoNetBank = $pesoNetBank;
        $this->send2banks = $send2banks;
        $this->logHistoryService = $logHistoryService;
        $this->transactionHistories = $transactionHistories;
        $this->smsService = $smsService;
        $this->emailService = $emailService;
    }

    public function validateTransaction(array $data, string $userId) {
        $user = $this->users->getUser($userId);
        $this->transactionValidationService->validateUser($user);

        $serviceFee = $this->serviceFees
            ->getByTierAndTransCategory($user->tier_id, TransactionCategoryIds::send2BankUBP);

        $serviceFeeAmount = $serviceFee ? $serviceFee->amount : 0;
        $totalAmount = $data['amount'] + $serviceFeeAmount;

        $this->transactionValidationService
            ->validate($user, TransactionCategoryIds::send2BankPesoNet, $totalAmount);

        return [
            'service_fee' => $serviceFeeAmount
        ];
    }

    public function transfer(array $data, string $userId) {

        \DB::beginTransaction();
        try {
            $transactionCategoryId = TransactionCategoryIds::send2BankUBP;
            $provider = TpaProviders::secBankPesonet;

            $user = $this->users->getUser($userId);
            $this->transactionValidationService->validateUser($user);

            $serviceFee = $this->serviceFees
                ->getByTierAndTransCategory($user->tier_id, $transactionCategoryId);

            $serviceFeeId = $serviceFee ? $serviceFee->id : '';
            $serviceFeeAmount = $serviceFee ? $serviceFee->amount : 0;
            $totalAmount = $data['amount'] + $serviceFeeAmount;

            $this->transactionValidationService
                ->validate($user, $transactionCategoryId, $totalAmount);


            $this->otpService->ensureValidated(OtpTypes::send2Bank . ':' . $userId);
            $refNo = rand(0, 999) . '-dev-testing';
            // $refNo = $this->referenceNumberService->generate(ReferenceNumberTypes::SendToBank);
            $data['refNo'] = $refNo;
            
            $currentDate = Carbon::now();
            $transactionDate = $currentDate->toDateTimeLocalString('millisecond');
            $otherPurpose = $data['other_purpose'] ?? '';

            $bank = $this->pesoNetBank->getByBankCode($data['bank_code']);
            $data['bank_name'] = $bank->bank_name;

            $reponse = $this->securityBankService->fundTransfer(TpaProviders::secBankPesonet, $data);
            $json_response = $reponse->json();

            if($json_response && isset($json_response['status']) && $json_response['status'] != 'SUCCESS') {
                $this->transactionFailed();
            }

            $account_name = $data['sender_first_name'] . ' ' . $data['sender_last_name'];
            $send2Bank = $this->send2banks->createTransaction($userId, $refNo, $data['bank_code'], $data['bank_name'],
            $account_name, $data['account_number'], $data['remarks'], $otherPurpose,
            $data['amount'], $serviceFeeAmount, $serviceFeeId, $currentDate, $transactionCategoryId, $provider,
            $data['sender_email'], $userId, $data['remarks'], "", json_encode($reponse->json()), $json_response['localRefId'], $json_response['status']);

            if (!$send2Bank) $this->transactionFailed();

            $balanceInfo = $user->balanceInfo;
            $balanceInfo->available_balance -= $totalAmount;
            if ($send2Bank->status === TransactionStatuses::pending) $balanceInfo->pending_balance += $totalAmount;
            if ($send2Bank->status === TransactionStatuses::failed) $balanceInfo->available_balance += $totalAmount;
            $balanceInfo->save();

            // Create transaction history
            if($send2Bank->status === TransactionStatuses::success) {
                $this->transactionHistories->log($userId, $transactionCategoryId, $send2Bank->id, $refNo,
                    $totalAmount, $send2Bank->transaction_date, request()->user()->id);
            }

            // CREATE LOG HISTORY
            $audit_remarks = request()->user()->account_number . " has transfered " . $totalAmount . " via PesoNet";

            $this->logHistoryService->logUserHistory($userId, $refNo, SquidPayModuleTypes::send2BankPesonet, get_class(new OutSend2Bank()), $transactionDate, $audit_remarks);

            $this->sendNotifications($user, $send2Bank, $balanceInfo->available_balance);

            \DB::commit();
            return $send2Bank;
        } catch (\Exception $e) {
            \DB::rollBack();
        }
    }
}
