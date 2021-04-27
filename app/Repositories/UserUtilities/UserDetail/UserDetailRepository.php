<?php

namespace App\Repositories\UserUtilities\UserDetail;

use App\Models\UserUtilities\UserDetail;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class UserDetailRepository extends Repository implements IUserDetailRepository
{
    public function __construct(UserDetail $model)
    {
        parent::__construct($model);
    }

    public function getByUserId(string $userId)
    {
        return $this->model->where('user_account_id', '=', $userId)->first();
    }

    public function getUserInfo(string $userAccountID)
    {
        return DB::table('user_details')
        ->leftJoin('user_accounts','user_accounts.id','=','user_details.user_account_id')
        ->leftJoin('user_balance_info','user_balance_info.user_account_id','=','user_accounts.id')
        ->leftJoin('user_transaction_histories','user_transaction_histories.user_account_id','=','user_balance_info.user_account_id')
        ->leftJoin('transaction_categories','transaction_categories.id','=','user_transaction_histories.transaction_category_id')
        ->leftJoin('tiers','tiers.id','=','user_accounts.tier_id')
        ->where('user_details.user_account_id',$userAccountID)
        ->select('user_details.first_name','user_details.middle_name','user_details.last_name','name_extension','user_details.user_account_status','user_accounts.mobile_number','user_accounts.email','user_details.selfie_location','user_balance_info.available_balance','tiers.name')
        ->get();
    }
}
