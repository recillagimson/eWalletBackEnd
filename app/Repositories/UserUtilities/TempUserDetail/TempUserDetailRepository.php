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

    public function getTempUserDetails()
    {
        return $this->model->where('status','=','pending')->where('created_at','<=',Carbon::now()->subDay())->count('status');
    }
}
