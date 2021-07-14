<?php

namespace App\Repositories\DrcrMemo;

use App\Enums\DrcrStatus;
use App\Enums\TransactionStatuses;
use App\Models\DrcrMemo;
use App\Models\DRCRProcedure;
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

    public function getListByCreatedBy(UserAccount $user, $data, $per_page = 15, $from = '', $to = '')
    {
        if ($data === 'P') $letterStatus = DrcrStatus::P;
        if ($data === 'D') $letterStatus = DrcrStatus::D;
        if ($data === 'A') $letterStatus = DrcrStatus::A;
        $records = $this->model
        ->with(['user_account', 'user_details', 'user_balance_info'])
        ->where('created_by', $user->id)
        ->orWhere('user_created', $user->id)
        ->where('status', $letterStatus);

        if($from && $to) {
            $records = $records->where('created_at', '>=', $from)
                    ->where('created_at', '<=', $to);
        }

        // ->paginate($per_page);
        return $records->get();
    }

    public function getAllPaginate($per_page = 15, $from = '', $to = '') {
        $records = $this->model
        ->with(['user_account', 'user_details', 'user_balance_info']);
        // ->paginate($per_page);
        if($from && $to) {
            $records = $records->where('created_at', '>=', $from)
                    ->where('created_at', '<=', $to);
        }
        return $records->get();
    }

    public function getAllList(UserAccount $user, $data, $per_page = 15, $from = '', $to = '')
    {
        if ($data === 'P') $letterStatus = DrcrStatus::P;
        if ($data === 'D') $letterStatus = DrcrStatus::D;
        if ($data === 'A') $letterStatus = DrcrStatus::A;
        $records = $this->model
            ->with(['user_account', 'user_details', 'user_balance_info'])
            ->where('status', $letterStatus);
            // ->paginate($per_page);

            if($from && $to) {
                $records = $records->where('created_at', '>=', $from)
                        ->where('created_at', '<=', $to);
            }

            return $records->get();
    }

   
    public function getList(UserAccount $user, $per_page = 15, $from = '', $to = '')
    {
        $records = $this->model
            ->with(['user_account', 'user_details', 'user_balance_info'])
            // ->where('created_by', $user->id)
            // ->orWhere('user_created', $user->id);
            ->where(function($q) use($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('user_created', $user->id);
            });

            if($from && $to) {
                // $records = $records->whereBetween('created_at', [$from, $to]);
                $records = $records->where('created_at', '>=', $from)
                    ->where('created_at', '<=', $to);
            }
            // ->paginate($per_page);
            // ->get();

            

        return $records->get();
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

    public function totalDRMemo()
    {
        return $this->model->where('created_at','<=',Carbon::now()->subDay())->where('type_of_memo','=','DR')->where('status','=','APPROVED')->sum('amount');
    }

    public function totalCRMemo()
    {
        return $this->model->where('created_at','<=',Carbon::now()->subDay())->where('type_of_memo','=','CR')->where('status','=','APPROVED')->sum('amount');
    }
    
    public function updateMemo(UserAccount $user, $data)
    {
        $status = $data['status'];
        if ($status === 'P') $letterStatus = DrcrStatus::P;
        if ($status === 'D') $letterStatus = DrcrStatus::D;
        if ($status === 'A') $letterStatus = DrcrStatus::A;
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
        return $this->model->where('user_created', '=', $UserID)->where('status', '=', 'pending')->where('created_at', '<=', Carbon::now()->subDay())->count('status');
    }

    public function reportData(string $from, string $to, string $filterBy = '', string $filterValue = '') {
        $record = DRCRProcedure::where('transaction_date', '>=', $from)
            ->where('transaction_date', '<=', $to);

        if($filterBy && $filterValue) {
            // IF CUSTOMER_ID
            if($filterBy == 'CUSTOMER_ID') {
                $record = $record->where('user_account_id', $filterValue);
            } 
            // IF CUSTOMER_NAME
            else if ($filterBy == 'CUSTOMER_NAME') {
                $record = $record->where(function($q) use($filterValue) {
                    $q->where('first_name', 'LIKE', '%' . $filterValue . '%')
                      ->orWhere('last_name', 'LIKE', '%' . $filterValue . '%');
                });
            }
            // IF TYPE
            else if($filterBy == 'TYPE') {
                $record = $record->where('transaction_type', $filterValue );
            }
            // IF STATUS
            else if($filterBy == 'STATUS') {
                $record = $record->where('Status', $filterValue);
            }
        }

        dd($record->get());
        return $record->get();
    }
}
