<?php

namespace App\Services\OutPayMerchant;

use App\Enums\Currencies;
use App\Enums\DrcrStatus;
use App\Enums\ReferenceNumberTypes;
use App\Enums\TransactionCategoryIds;
use App\Enums\TransactionStatuses;
use App\Repositories\DrcrMemo\IDrcrMemoRepository;
use App\Repositories\OutPayMerchants\IOutPayMerchantRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use Carbon\Carbon;
use FontLib\TrueType\Collection;

class PayMerchantService implements IPayMerchantService
{
    private IOutPayMerchantRepository $payments;
    private IDrcrMemoRepository $memos;
    private IUserTransactionHistoryRepository $transactionHistories;
    private IReferenceNumberService $refNoService;
    private IUserAccountRepository $userAccounts;

    public function __construct(IOutPayMerchantRepository         $payments,
                                IDrcrMemoRepository               $memos,
                                IUserTransactionHistoryRepository $transactionHistories,
                                IReferenceNumberService           $refNoService,
                                IUserAccountRepository            $userAccounts)
    {
        $this->payments = $payments;
        $this->memos = $memos;
        $this->transactionHistories = $transactionHistories;
        $this->refNoService = $refNoService;
        $this->userAccounts = $userAccounts;
    }

    public function pay(array $data): array
    {
        $refNo = $this->refNoService->generate(ReferenceNumberTypes::PayMerchant);
        $memoRefNo = $this->refNoService->generate(ReferenceNumberTypes::DR);
        $user = $this->userAccounts->getUserByAccountNumber($data['account_number']);

        $payment = $this->payments->create([
            'user_account_id' => $user->id,
            'merchant_account_number' => $data['merchant_account_number'],
            'reference_number' => $refNo,
            'amount' => $data['amount'],
            'total_amount' => $data['amount'],
            'transaction_date' => Carbon::now(),
            'transaction_category_id' => TransactionCategoryIds::payMerchant,
            'status' => TransactionStatuses::success,
            'user_created' => $user->id,
            'user_updated' => $user->id,
        ]);

        $memo = $this->memos->create([
            'user_account_id' => $user->id,
            'type_of_memo' => ReferenceNumberTypes::DR,
            'reference_number' => $memoRefNo,
            'transaction_category_id' => TransactionCategoryIds::drMemo,
            'amount' => $data['amount'],
            'currency_id' => Currencies::philippinePeso,
            'category' => 'Adjustment',
            'description' => 'Debit Memo for Pay Merchant',
            'stataus' => DrcrStatus::A,
            'created_by' => $user->id,
            'approved_at' => $payment->transaction_date
        ]);

        $balanceInfo = $user->balanceInfo;
        $balanceInfo->available_balance -= $data['available_balance'];
        $balanceInfo->save();

        $this->transactionHistories->log($user->id, TransactionCategoryIds::drMemo,
            $payment->id, $refNo, $data['amount'], $payment->transaction_date, $user->id);

        return [
            'account_number' => $data['account_number'],
            'total_amount' => $data['amount'],
            'reference_number' => $payment->reference_number,
            'transaction_date' => $payment->transaction_date,
            'merchant_account_number' => $data['merchant_account_number']
        ];
    }

    public function getByReferenceNumber(string $refNo): Collection
    {
        return $this->payments->getByRefNo($refNo);
    }

}
