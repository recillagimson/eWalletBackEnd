<?php

namespace App\Services\Send2BankSecBank;

use App\Enums\TpaProviders;
use App\Enums\ReferenceNumberTypes;
use App\Enums\TransactionCategoryIds;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\ThirdParty\SecurityBank\ISecurityBankService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;

class PesoNetService implements IPesoNetService
{   

    private IUserAccountRepository $users;
    private IServiceFeeRepository $serviceFees;
    private ITransactionValidationService $transactionValidationService;
    private ISecurityBankService $securityBankService;
    private IReferenceNumberService $referenceNumberService;

    public function __construct(IUserAccountRepository $users, IServiceFeeRepository $serviceFees, ITransactionValidationService $transactionValidationService, ISecurityBankService $securityBankService, IReferenceNumberService $referenceNumberService)
    {
        $this->users = $users;
        $this->serviceFees = $serviceFees;
        $this->transactionValidationService = $transactionValidationService;
        $this->securityBankService = $securityBankService;
        $this->referenceNumberService = $referenceNumberService;
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
        $data['refNo'] = $this->referenceNumberService->generate(ReferenceNumberTypes::SendToBank);
        $reponse = $this->securityBankService->fundTransfer(TpaProviders::secBankPesonet, $data);
        $json_response = $reponse->json();
        dd($json_response);
    }
}
