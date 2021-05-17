<?php


namespace App\Repositories\Send2Bank;

use App\Enums\TpaProviders;
use App\Enums\TransactionStatuses;
use App\Models\OutSend2Bank;
use App\Repositories\Repository;
use Carbon\Carbon;

class OutSend2BankRepository extends Repository implements IOutSend2BankRepository
{
    public function __construct(OutSend2Bank $model)
    {
        parent::__construct($model);
    }

    public function getByReferenceNo(string $refNo)
    {
        return $this->model->where('reference_number', $refNo)->first();
    }

    public function getPending(string $userId)
    {
        return $this->model->where([
            'user_account_id' => $userId,
            'status' => TransactionStatuses::pending
        ])->get();
    }


    public function createTransaction(string $userId, string $refNo, string $bankCode, string $bankName, string $accountName,
                                      string $accountNumber, string $purpose, string $otherPurpose, float $amount,
                                      float $serviceFee, string $serviceFeeId, Carbon $transactionDate,
                                      string $transactionCategoryId, string $provider, string $sendReceiptTo, string $userCreated)
    {
        $data = [
            'user_account_id' => $userId,
            'reference_number' => $refNo,
            'bank_code' => $bankCode,
            'bank_name' => $bankName,
            'account_name' => $accountName,
            'account_number' => $accountNumber,
            'amount' => $amount,
            'service_fee' => $serviceFee,
            'total_amount' => $amount + $serviceFee,
            'service_fee_id' => $serviceFeeId,
            'transaction_date' => $transactionDate,
            'transaction_category_id' => $transactionCategoryId,
            'provider' => $provider,
            'send_receipt_to' => $sendReceiptTo,
            'purpose' => $purpose,
            'other_purpose' => $otherPurpose,
            'status' => TransactionStatuses::pending,
            'user_created' => $userCreated,
        ];

        return $this->create($data);
    }

    public function getPendingDirectTransactionsByAuthUser() {
        $records = $this->model->where('user_account_id', request()->user()->id)
            ->where('status', TransactionStatuses::pending)
            ->where('provider', TpaProviders::ubpDirect)
            ->get();
        return $records;
    }

    public function getByUserAccountIDBetweenDates(string $userId, Carbon $startDate, Carbon $endDate)
    {
        return $this->model
            ->where('user_account_id', $userId)
            ->where('status', '!=', TransactionStatuses::failed)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }
}
