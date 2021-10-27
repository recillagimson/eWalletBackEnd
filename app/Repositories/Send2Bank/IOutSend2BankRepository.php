<?php


namespace App\Repositories\Send2Bank;


use App\Repositories\IRepository;
use Carbon\Carbon;

interface IOutSend2BankRepository extends IRepository
{
    public function getByReferenceNo(string $refNo);

    public function getPending(string $userId);

    public function getUsersWithPending();

    public function getPendingDirectTransactionsByAuthUser();

    public function createTransaction(string $userId, string $refNo, string $bankCode, string $bankName, string $accountName,
                                      string $accountNumber, string $purpose, string $otherPurpose, float $amount,
                                      float  $serviceFee, string $serviceFeeId, Carbon $transactionDate,
                                      string $transactionCategoryId, string $provider, string $sendReceiptTo, string $userCreated, ?string $remarks = "", ?string $particulars = "");

    public function getSumOfTransactions($from, $to, string $userAccountId);

    public function totalSend2Bank();

}
