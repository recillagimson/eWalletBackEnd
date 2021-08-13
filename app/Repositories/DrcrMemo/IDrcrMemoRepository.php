<?php
namespace App\Repositories\DrcrMemo;

use App\Models\UserAccount;
use App\Repositories\IRepository;

interface IDrcrMemoRepository extends IRepository
{
    public function getByUserAccountID(UserAccount $user);
    public function getAllList(UserAccount $user, $data, $per_page = 15, $from = '', $to = '');
    public function getAllPaginate($per_page = 15, $from = '', $to = '');
    public function getList(UserAccount $user, $per_page = 15, $from = '', $to = '');
    public function getListByCreatedBy(UserAccount $user, $data, $per_page = 15, $from = '', $to = '');
    public function getPendingByCreatedBy(UserAccount $user);
    public function getByReferenceNumber(string $referenceNumber);
    public function updateDrcr(UserAccount $user, $data);
    public function totalDRMemo();
    public function totalCRMemo();
    public function updateMemo(UserAccount $user, $data);
    public function getPerUser(string $UserID);
    public function reportData(string $from, string $to, string $filterBy, string $filterValue);
    public function reportDataFarmers(string $from, string $to, string $filterBy = '', string $filterValue = '', $type);
}
