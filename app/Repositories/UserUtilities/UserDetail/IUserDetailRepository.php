<?php

namespace App\Repositories\UserUtilities\UserDetail;

use App\Models\UserAccount;
use App\Repositories\IRepository;

interface IUserDetailRepository extends IRepository
{
    public function getByUserId(string $userId);
    public function getFarmers($from, $to, $filterBy, $filterValue, $type);
    public function getIsExistingByNameAndBirthday($firstname, $middename, $lastname, $birthday);
}
