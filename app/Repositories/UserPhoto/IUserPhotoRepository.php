<?php

namespace App\Repositories\UserPhoto;

use App\Repositories\IRepository;

interface IUserPhotoRepository extends IRepository
{
    public function updateSelfiePhoto(string $selfieUrl);
    public function updateAvatarPhoto(string $avatarUrl);
}
