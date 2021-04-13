<?php

namespace App\Services\Utilities\LogHistory;

interface ILogHistoryService {
    public function logUserHistory(string $user_account_id, string $reference_number = null, string $squidpay_module = null, string $namespace = null, $transaction_date, string $remarks, string $operation = null);
}
