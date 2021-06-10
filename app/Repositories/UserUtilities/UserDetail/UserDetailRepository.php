<?php

namespace App\Repositories\UserUtilities\UserDetail;

use App\Models\UserUtilities\UserDetail;
use App\Models\UserAccount;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class UserDetailRepository extends Repository implements IUserDetailRepository
{
    public function __construct(UserDetail $model)
    {
        parent::__construct($model);
    }

    public function getByUserId(string $userAccountID)
    {
        $record =  $this->model->where('user_account_id', '=', $userAccountID)->first();

        if($record) {
            return $record->append('avatar_link');
        }

        ValidationException::withMessages([
            'user_detail_not_found' => 'User Detail not found'
        ]);
    }
}
