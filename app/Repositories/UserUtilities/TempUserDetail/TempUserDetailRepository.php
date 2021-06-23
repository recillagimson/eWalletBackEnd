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
        $result = $this->model->find($id);
        
        return $result;
    }

    public function getAllPaginated($perPage = 10) {
        $result = $this->model->orderBy('created_at', 'DESC')->paginate($perPage);
        
        return $result;
    }

    public function getLatestByUserId($id) {
        $result = $this->model->where('user_account_id', $id)->orderBy('created_at', 'DESC')->first();
        
        return $result;
    }

    public function denyByUserId($id, $user) {
        $result = $this->model->where('user_account_id', $id)->update(['status' => 'DISAPPROVED', 'declined_by' => $user->id, 'declined_date' => Carbon::now()]);
        
        return $result;
    }

}
