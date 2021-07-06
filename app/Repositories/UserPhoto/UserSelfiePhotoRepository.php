<?php

namespace App\Repositories\UserPhoto;

use App\Models\UserPhoto;
use App\Models\UserSelfiePhoto;
use App\Models\UserUtilities\UserDetail;
use App\Repositories\Repository;
use App\Traits\Errors\WithUserErrors;
use Illuminate\Validation\ValidationException;

class UserSelfiePhotoRepository extends Repository implements IUserSelfiePhotoRepository
{
    use WithUserErrors;

    public function __construct(UserSelfiePhoto $model)
    {
        parent::__construct($model);
    }

    public function getSelfieByAccountNumber(string $userAccountId) {
        $record = $this->model
            ->where('user_account_id', $userAccountId)
            ->orderBy('created_at', 'DESC')
            ->first();
        if($record) {
            return $record;
        }

        $this->userSelfieNotFound();
    }
}
