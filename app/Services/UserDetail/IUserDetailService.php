<?php
namespace App\Services\UserDetail;

interface IUserDetailService {

    public function addOrUpdate(object $userAccount, array $details);
}
