<?php

namespace App\Repositories\UserUtilities\UserDetail;

use App\Models\UserUtilities\UserDetail;
use App\Models\UserAccount;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class UserDetailRepository extends Repository implements IUserDetailRepository
{
    public function __construct(UserDetail $model)
    {
        parent::__construct($model);
    }

    public function getByUserId(string $userAccountID)
    {
        return $this->model->where('user_account_id', '=', $userAccountID)->first();
    }
}
