<?php

namespace App\Services\Utilities\Verification;

use App\Repositories\UserAccount\ILogHistoryRepository;

class LogService implements ILogService
{
    public ILogHistoryRepository $iLogHistoryRepository;

    public function __construct(ILogHistoryRepository $iLogHistoryRepository)
    {
        $this->iLogHistoryRepository = $iLogHistoryRepository;
    }

    public function logUserHistory(string $user_account_id, string $reference_number = null, string $squidpay_module = null, string $namespace = null, $transaction_date, string $remarks, string $operation = null) {
        $record = $this->ILogHistoryRepository->create([
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
}
