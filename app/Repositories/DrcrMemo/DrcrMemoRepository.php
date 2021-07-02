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

    public function getListByCreatedBy(UserAccount $user, $data, $per_page = 15)
    {
        if ($data === 'P') $letterStatus = DrcrStatus::P;
        if ($data === 'D') $letterStatus = DrcrStatus::D;
        if ($data === 'A') $letterStatus = DrcrStatus::A;
        return $this->model->where('created_by', $user->id)->orWhere('user_created', $user->id)->where('status', $letterStatus)->paginate($per_page);
    }

    public function getAllPaginate($per_page = 15) {
        return $this->model->paginate($per_page);
    }

    public function getAllList(UserAccount $user, $data, $per_page = 15)
    {
        if ($data === 'P') $letterStatus = DrcrStatus::P;
        if ($data === 'D') $letterStatus = DrcrStatus::D;
        if ($data === 'A') $letterStatus = DrcrStatus::A;
        return $this->model->where('status', $letterStatus)->paginate($per_page);
    }

   
    public function getList(UserAccount $user, $per_page = 15)
    {
        return $this->model->where('created_by', $user->id)->orWhere('user_created', $user->id)->paginate($per_page);
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
            $this->model->where('reference_number', $data['referenceNumber'])->update([
                'status' => DrcrStatus::A,
                'remarks' => 'Approved Dr/Cr Memo',
                'approved_by' => $user->id,
                'approved_at' => Carbon::now(),
                'user_updated' => $user->id
            ]);
        }
        if ($data['status'] == DrcrStatus::Decline) {
            $this->model->where('reference_number', $data['referenceNumber'])->update([
                'status' => DrcrStatus::D,
                'remarks' => $data['remarks'],
                'declined_by' => $user->id,
                'declined_at' => Carbon::now(),
                'user_updated' => $user->id
            ]);
        }
        return $this->getByReferenceNumber($data['referenceNumber'])->toArray();
    }

    public function updateMemo(UserAccount $user, $data)
    {
        $status = $data['status'];
        if($status === 'P') $letterStatus = DrcrStatus::P;
        if($status === 'D') $letterStatus = DrcrStatus::D;
        if($status === 'A') $letterStatus = DrcrStatus::A;
        $this->model->where('reference_number', $data['referenceNumber'])->update([
            'status' => $letterStatus,
            'type_of_memo' => $data['typeOfMemo'],
            'amount' => $data['amount'],
            'category' => $data['category'],
            'description' => $data['description'],
            'user_updated' => $user->id
        ]);
        return $this->getByReferenceNumber($data['referenceNumber'])->toArray();
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
