<?php

namespace App\Repositories\UserUtilities\TempUserDetail;

use App\Models\TempUserDetail;
use App\Models\UserAccount;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
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

    public function getAllPaginated($attributes, $perPage = 10) {
        $result = $this->model;

        if (isset($attributes['filter_by']) && isset($attributes['filter_value'])) {
            $result = $result->where($attributes['filter_by'], 'LIKE', "%" . $attributes['filter_value'] . "%");
        }

        if (isset($attributes['from']) && isset($attributes['to'])) {
            $result = $result->whereBetween('created_at', [$attributes['from'], $attributes['to']]);
        }

        return $result->with([
            'user:id,account_number,tier_id', 
        ])->orderBy('created_at', 'DESC')->paginate($perPage);
    }

    public function getLatestByUserId($id) {
        $result = $this->model->where('user_account_id', $id)->orderBy('created_at', 'DESC')->first();
        
        return $result;
    }

    public function denyByUserId($id, $user) {
        $result = $this->model->where('user_account_id', $id)->update(['status' => 'DISAPPROVED', 'declined_by' => $user->id, 'declined_date' => Carbon::now()]);
        
        return $result;
    }

    public function getTempUserDetails()
    {
        return $this->model->where('status','=','pending')->where('created_at','<=',Carbon::now()->subDay())->count('status');
    }
}
