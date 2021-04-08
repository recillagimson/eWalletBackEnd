<?php

namespace App\Services\Auth;

use App\Repositories\Client\IClientRepository;
use App\Repositories\UserPhoto\IUserPhotoRepository;

class VerificationService implements IVerificationService
{
    public IUserPhotoRepository $userPhotoRepository;

    public function __construct(IUserPhotoRepository $userPhotoRepository)
    {
        $this->userPhotoRepository = $userPhotoRepository;
    }
}
