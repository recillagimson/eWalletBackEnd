<?php
namespace App\Repositories\DrcrMemo;

use App\Models\UserAccount;
use App\Repositories\IRepository;

interface IDrcrMemoRepository extends IRepository
{
    public function getByUserAccountID(UserAccount $user);
    public function getListByCreatedBy(UserAccount $user);
    public function getPendingByCreatedBy(UserAccount $user);
    public function getByReferenceNumber(string $referenceNumber);
    public function updateDrcr(UserAccount $user, $data);
    public function getDRCRMemo();
    public function getPerUser(string $UserID);
}
