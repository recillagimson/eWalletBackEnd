<?php

namespace App\Services\Utilities\LogHistory;

use App\Repositories\LogHistory\ILogHistoryRepository;


class LogHistoryService implements ILogHistoryService
{
    public ILogHistoryRepository $logHistoryRepository;

    public function __construct(ILogHistoryRepository $logHistoryRepository)
    {
        $this->logHistoryRepository = $logHistoryRepository;
    }

    public function logUserHistory(string $user_account_id, string $reference_number = null, string $squidpay_module = null, string $namespace = null, $transaction_date, string $remarks, string $operation = null) {
        $record = $this->logHistoryRepository->create([
            'user_account_id' => $user_account_id,
            "reference_number" => $user_account_id,
            "squidpay_module" => $squidpay_module,
            "namespace" => $namespace,
            "transaction_date" => $transaction_date,
            "remarks" => $remarks,
            "operation" => $operation,
            "user_created" => request()->user()->id,
            "user_updated" => request()->user()->id,
        ]);

        return $record;
    }

    public function logUserHistoryUnauthenticated(string $user_account_id, string $reference_number = null, string $squidpay_module = null, string $namespace = null, $transaction_date, string $remarks, string $operation = null) {
        $record = $this->logHistoryRepository->create([
            'user_account_id' => $user_account_id,
            "reference_number" => $user_account_id,
            "squidpay_module" => $squidpay_module,
            "namespace" => $namespace,
            "transaction_date" => $transaction_date,
            "remarks" => $remarks,
            "operation" => $operation,
            "user_created" => $user_account_id,
            "user_updated" => $user_account_id,
        ]);

        return $record;
    }
}
