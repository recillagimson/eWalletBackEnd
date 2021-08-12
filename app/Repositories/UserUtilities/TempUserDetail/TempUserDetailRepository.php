<?php

namespace App\Repositories\UserUtilities\TempUserDetail;

use App\Models\TempUserDetail;
use App\Repositories\Repository;
use Carbon\Carbon;

class TempUserDetailRepository extends Repository implements ITempUserDetailRepository
{
    public function __construct(TempUserDetail $model)
    {
        parent::__construct($model);
    }

    public function findById($id)
    {
        $result = $this->model->with('latestTierApproval:id,user_account_id,status,approved_by,approved_date')->find($id);

        return $result;
    }

    public function getAllPaginated($perPage = 10)
    {
        $result = $this->model->with([
            'user:id,account_number,tier_id',
        ])->orderBy('created_at', 'DESC')->paginate($perPage);

        return $result;
    }

    public function getLatestByUserId($id)
    {
        $result = $this->model->where('user_account_id', $id)->orderBy('created_at', 'DESC')->first();

        return $result;
    }

    public function denyByUserId($id, $user)
    {
        $result = $this->model->where('user_account_id', $id)->update(['status' => 'DISAPPROVED', 'declined_by' => $user->id, 'declined_date' => Carbon::now()]);

        return $result;
    }

    public function getTempUserDetails()
    {
        return $this->model->where('status','=','pending')->where('created_at','<=',Carbon::now()->subDay())->count('status');
    }
}
