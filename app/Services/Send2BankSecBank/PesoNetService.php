<?php

namespace App\Services\Send2BankSecBank;

use App\Enums\TransactionCategoryIds;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Transaction\ITransactionValidationService;

class PesoNetService implements IPesoNetService
{   

    private IUserAccountRepository $users;
    private IServiceFeeRepository $serviceFees;
    private ITransactionValidationService $transactionValidationService;


    public function __construct(IUserAccountRepository $users, IServiceFeeRepository $serviceFees, ITransactionValidationService $transactionValidationService)
    {
        $this->users = $users;
        $this->serviceFees = $serviceFees;
        $this->transactionValidationService = $transactionValidationService;
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
}
