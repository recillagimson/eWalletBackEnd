<?php


namespace App\Repositories\Send2Bank;


use App\Repositories\IRepository;
use Carbon\Carbon;

interface IOutSend2BankRepository extends IRepository
{
    public function getPending(string $userId);

    public function createTransaction(string $userId, string $refNo, string $accountName, string $accountNumber, string $message,
                                      float $amount, float $serviceFee, string $serviceFeeId, Carbon $transactionDate, string $transactionCategoryId,
                                      string $provider, string $notifyType, string $notifyTo, string $userCreated);

    public function getPendingDirectTransactionsByAuthUser();
}
