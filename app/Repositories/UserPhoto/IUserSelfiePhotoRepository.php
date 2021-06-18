<?php

namespace App\Repositories\UserPhoto;

use App\Repositories\IRepository;

interface IUserSelfiePhotoRepository extends IRepository
{
    public function getSelfieByAccountNumber(string $accountNumber);
}
