<?php

namespace App\Repositories\UserPhoto;

use App\Models\UserPhoto;
use App\Models\UserSelfiePhoto;
use App\Models\UserUtilities\UserDetail;
use App\Repositories\Repository;
use Illuminate\Validation\ValidationException;

class UserSelfiePhotoRepository extends Repository implements IUserSelfiePhotoRepository
{
    public function __construct(UserSelfiePhoto $model)
    {
        parent::__construct($model);
    }
}
