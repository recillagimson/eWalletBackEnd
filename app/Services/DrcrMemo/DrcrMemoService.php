<?php

namespace App\Services\DrcrMemo;

use App\Enums\Currencies;
use App\Enums\DrcrStatus;
use App\Enums\ReferenceNumberTypes;
use App\Enums\TransactionCategoryIds;
use App\Enums\TransactionStatuses;
use App\Models\UserAccount;
use App\Repositories\DrcrMemo\IDrcrMemoRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithDrcrMemoErrors;

class DrcrMemoService implements IDrcrMemoService
{
    use WithDrcrMemoErrors;

    private IDrcrMemoRepository $drcrMemoRepository;
    private IReferenceNumberService $referenceNumberService;
    private IUserAccountRepository $userAccountRepository;
    private IUserBalanceInfoRepository $userBalanceRepository;

    public function __construct(IDrcrMemoRepository $drcrMemoRepository,
                                IReferenceNumberService $referenceNumberService,
                                IUserAccountRepository $userAccountRepository,
                                IUserBalanceInfoRepository $userBalanceRepository)
    {
        $this->drcrMemoRepository = $drcrMemoRepository;
        $this->referenceNumberService = $referenceNumberService;
        $this->userAccountRepository = $userAccountRepository;
        $this->userBalanceRepository = $userBalanceRepository;
    }

    public function getList(UserAccount $user)
    {
        return $this->drcrMemoRepository->getListByCreatedBy($user);
    }


    public function show(string $referenceNumber): array
    {
        $show = $this->drcrMemoRepository->getByReferenceNumber($referenceNumber);
        return [$show];
    }


    public function getUser(string $accountNumber): array
    {
        $user = $this->userAccountRepository->getUserByAccountNumber($accountNumber);
        $customerName = $user->userDetail->first_name . ' ' . $user->userDetail->last_name;
        $balance = $user->balanceInfo->available_balance;
        return ['customer_name' => $customerName, 'balance' => $balance];
    }


    public function showPending(UserAccount $user)
    {
        return $this->drcrMemoRepository->getPendingByCreatedBy($user);
    }


    public function store(UserAccount $user, $data)
    {
        $customer = $this->getUserByAccountNumber($data);
        if ($data['typeOfMemo'] == ReferenceNumberTypes::DR) {
            $isEnough = $this->checkAmount($data, $customer->id);
            if (!$isEnough) $this->insuficientBalance();
        }
        return $this->drcrMemo($user, $data, $customer->id);
    }


    public function approval(UserAccount $user, $data): array
    {
        $drcrMemo = $this->drcrMemoRepository->getByReferenceNumber($data['referenceNumber']);
        $data['amount'] = $drcrMemo->amount;

        $isEnough = $this->checkAmount($data, $drcrMemo->user_account_id);
        if (!$isEnough) $this->insuficientBalance();

        if ($data['status'] == DrcrStatus::Approve) {
            if ($drcrMemo->type_of_memo == ReferenceNumberTypes::DR) {
                $this->debitMemo($drcrMemo->user_account_id, $drcrMemo->amount);
            }
            if ($drcrMemo->type_of_memo == ReferenceNumberTypes::CR) {
                $this->creditMemo($drcrMemo->user_account_id, $drcrMemo->amount);
            }
        }

        if ($this->drcrMemoRepository->updateDrcr($user, $data)) return ['status' => 'success'];
        return ['status' => 'failed'];
    }


    // PRIVATE METHODS

    private function debitMemo($userID, $amount)
    {
        $balance = $this->userBalanceRepository->getUserBalance($userID);
        $balance -= $amount;
        $this->userBalanceRepository->updateUserBalance($userID, $balance);
    }

    private function creditMemo($userID, $amount)
    {
        $balance = $this->userBalanceRepository->getUserBalance($userID);
        $balance += $amount;
        $this->userBalanceRepository->updateUserBalance($userID, $balance);
    }

    private function checkAmount($data, $customerID): bool
    {
        $balance = $this->userBalanceRepository->getUserBalance($customerID);
        if ($balance >= $data['amount']) return true;
    }

    private function setTypeOfMemo(string $typeOfMemo): string
    {
        if ($typeOfMemo === ReferenceNumberTypes::DR) return ReferenceNumberTypes::DR;
        return ReferenceNumberTypes::CR;
    }

    private function setTransactionCategory(string $typeOfMemo): string
    {
        if ($typeOfMemo === ReferenceNumberTypes::DR) return TransactionCategoryIds::drMemo;
        return TransactionCategoryIds::crMemo;
    }

    private function getReference($data): string
    {
        return $this->referenceNumberService->generate($this->setTypeOfMemo($data));
    }

    private function getUserByAccountNumber($data)
    {
        return $this->userAccountRepository->getUserByAccountNumber($data['accountNumber']);
    }

    private function drcrMemo(UserAccount $user, $data, string $customerID)
    {
        $memoType = $this->setTypeOfMemo($data['typeOfMemo']);
        $refNo = $this->referenceNumberService->generate($memoType);
        $tranCatId = $this->setTransactionCategory($data['typeOfMemo']);
        $newMemo = [
            'user_account_id' => $customerID,
            'type_of_memo' => $data['typeOfMemo'],
            'reference_number' => $refNo,
            'transaction_category_id' => $tranCatId,
            'amount' => $data['amount'],
            'currency_id' => Currencies::philippinePeso,
            'category' => $data['category'],
            'description' => $data['description'],
            'status' => TransactionStatuses::pending,
            'user_created' => $user->id
        ];

        return $this->drcrMemoRepository->create($newMemo);
    }

}
