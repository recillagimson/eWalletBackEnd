<?php

namespace App\Repositories\DrcrMemo;

use App\Enums\DrcrStatus;
use App\Enums\TransactionStatuses;
use App\Models\DrcrMemo;
use App\Models\UserAccount;
use App\Repositories\Repository;
use Carbon\Carbon;

class DrcrMemoRepository extends Repository implements IDrcrMemoRepository
{
    public function __construct(DrcrMemo $model)
    {
        parent::__construct($model);
    }

    public function getByUserAccountID(UserAccount $user)
    {
        return $this->model->where('user_account_id', $user->id)->get();
    }

    public function getListByCreatedBy(UserAccount $user)
    {
        return $this->model->where('created_by', $user->id)->get();
    }

    public function getPendingByCreatedBy(UserAccount $user)
    {
        return $this->model->where('status', TransactionStatuses::pending)->get();
    }

    public function getByReferenceNumber(string $referenceNumber)
    {
        return $this->model->where('reference_number', $referenceNumber)->first();
    }

    public function updateDrcr(UserAccount $user, $data)
    {
        if($data['status'] == DrcrStatus::Approve){
            return $this->model->where('reference_number', $data['referenceNumber'])->update([
                'status' => DrcrStatus::A,
                'approved_by' => $user->id,
                'approved_at' => Carbon::now(),
                'user_updated' => $user->id
            ]);
        }
        if ($data['status'] == DrcrStatus::Decline) {
            return $this->model->where('reference_number', $data['referenceNumber'])->update([
                'status' => DrcrStatus::D,
                'declined_by' => $user->id,
                'declined_at' => Carbon::now(),
                'user_updated' => $user->id
            ]);
        }
    }

    public function getDRCRMemo()
    {
        return $this->model->where('status', '=', 'pending')->where('created_at', '<=', Carbon::now()->subDay())->count('status');
    }

    public function getPerUser(string $UserID)
    {
        return $this->model->where('created_by', '=', $UserID)->where('status', '=', 'pending')->where('created_at', '<=', Carbon::now()->subDay())->count('status');
    }

}
