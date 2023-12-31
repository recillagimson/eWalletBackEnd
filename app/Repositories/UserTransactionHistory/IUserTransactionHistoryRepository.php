<?php

namespace App\Repositories\UserTransactionHistory;

use App\Repositories\IRepository;
use Carbon\Carbon;

interface IUserTransactionHistoryRepository extends IRepository
{
    public function getTotalTransactionAmountByUserAccountIdDateRange(string $userAccountId, string $from, $to);

    public function log(string $userId, string $transactionCategoryId, string $transactionId, string $refNo,
                        float $totalAmount, Carbon $transactionDate, string $userCreated);

    public function getByAuthUser();

    public function findTransactionWithRelation(string $id);

    public function getTransactionHistoryByIdAndDateRange(string $userAccountId, string $from, string $to);

    public function countTransactionHistoryByDateRangeWithAmountLimitWithPaginate(string $from, string $to);

    public function countTransactionHistoryByDateRangeWithAmountLimit(string $from, string $to);

    public function isExisting(string $id);

    public function getByAuthUserViaViews(string $status);

    public function findTransactionWithRelationViaView(string $id);

    public function getTransactionHistoryAdmin(array $attr);

    public function getTransactionHistoryAdminFarmer(array $attr);

    public function getDBPTransactionHistory(array $attr, string $authUser);

    public function getFilteredTransactionHistory(string $authUser, string $from, string $to);
    
}
