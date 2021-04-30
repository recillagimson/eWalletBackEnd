<?php


namespace App\Repositories\Send2Bank;


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

    public function getPending(string $userId)
    {
        return $this->model->where([
            'user_account_id' => $userId,
            'status' => TransactionStatuses::pending
        ])->get();
    }

    public function createTransaction(string $userId, string $refNo, string $accountName, string $accountNumber, string $message,
                                      float $amount, float $serviceFee, string $serviceFeeId, Carbon $transactionDate, string $transactionCategoryId,
                                      string $provider, string $notifyType, string $notifyTo, string $userCreated)
    {
        $data = [
            'user_account_id' => $userId,
            'reference_number' => $refNo,
            'account_name' => $accountName,
            'account_number' => $accountNumber,
            'message' => $message,
            'amount' => $amount,
            'service_fee' => $serviceFee,
            'total_amount' => $amount + $serviceFee,
            'service_fee_id' => $serviceFeeId,
            'transaction_date' => $transactionDate,
            'transaction_category_id' => $transactionCategoryId,
            'provider' => $provider,
            'notify_type' => $notifyType,
            'notify_to' => $notifyTo,
            'status' => TransactionStatuses::pending,
            'user_created' => $userCreated,
        ];

        return $this->create($data);
    }
}
