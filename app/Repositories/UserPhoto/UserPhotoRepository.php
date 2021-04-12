<?php

namespace App\Repositories\UserPhoto;

use App\Models\UserPhoto;
use App\Repositories\Repository;

class UserPhotoRepository extends Repository implements IUserPhotoRepository
{
    public function __construct(UserPhoto $model)
    {
        parent::__construct($model);
    }
}
