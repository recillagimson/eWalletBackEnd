<?php

namespace App\Repositories\Tier;

use App\Models\TierApproval;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class TierApprovalRepository extends Repository implements ITierApprovalRepository
{
    public function __construct(TierApproval $model)
    {
        parent::__construct($model);
    }

    public function getPendingApprovalRequestByUserAccountId(string $id) {
        return $this->model->where('user_account_id', $id)
            ->where('status', 'PENDING')
            ->first();
    }

    public function getPendingApprovalRequest() {
        return $this->model->where('user_account_id', request()->user()->id)
            ->where('status', 'PENDING')
            ->first();
    }

    public function updateOrCreateApprovalRequest(array $attr) {
        $record = $this->model
            ->where('user_account_id', $attr['user_account_id'])
            ->orderBy('created_at', 'DESC')
            ->first();

        if($record) {
            return $record;
        }

        return $this->model->create($attr);
    }

    public function list(array $attr) {
        $from = Carbon::now()->subDays(30)->format('Y-m-d');
        $to = Carbon::now()->format('Y-m-d');
        $records = $this->model->with(['user_account', 'user_detail']);

        if (isset($attr['from']) && isset($attr['to'])) {
            $from = $attr['from'];
            $to = $attr['to'];
        }

        $records = $records->whereHas('user_account', function ($query) use ($from, $to) {
            $query->whereBetween('created_at', [$from, $to]);
        });

        if (isset($attr['filter_by']) && isset($attr['filter_value'])) {
            $filter_by = $attr['filter_by'];
            $filter_value = $attr['filter_value'];

            // IF CUSTOMER NAME
            if ($filter_by === 'CUSTOMER_NAME') {
                $records = $records->whereHas('user_detail', function ($query) use ($filter_value) {
                    $query->where(function ($q) use ($filter_value) {
                        $q->where('first_name', 'LIKE', '%' . $filter_value . '%')
                            ->orWhere('middle_name', 'LIKE', '%' . $filter_value . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $filter_value . '%');
                    });
                });
            } // IF CUSTOMER ID
            else if ($filter_by === 'CUSTOMER_ID') {
                $records = $records->whereHas('user_account', function ($query) use ($filter_value) {
                    $query->where('account_number', $filter_value);
                });
            } // IF STATUS
            else if ($filter_by === 'STATUS') {
                $records = $records->where('status', $filter_value);
            }
            // IF EMAIL
            else if ($filter_by === 'EMAIL') {
                $records = $records->whereHas('user_account', function ($query) use ($filter_value) {
                    $query->where('email', 'LIKE', '%' . $filter_value . '%');
                });
            }
            // IF MOBILE
            else if ($filter_by === 'MOBILE') {
                $records = $records->whereHas('user_account', function ($query) use ($filter_value) {
                    $query->where('mobile_number', $filter_value);
                });
            }
        }

        return $records->paginate();
    }

    public function showTierApproval(TierApproval $tierApproval) {
        $data = $this->model->with([
            'id_photos',
            'selfie_photos',
            'id_photos.id_type',
            'id_photos.reviewer:user_details.id,first_name,last_name,middle_name',
            'user_account',
            'user_detail'
        ])->find($tierApproval->id);

        if($data) {
            return $data;
        }
        return ValidationException::withMessages([
            'tier_approval_not_found' => 'Tier Approval Request not found'
        ]);
    }

    public function getTierApproval()
    {
        return $this->model->where('created_at','<=',Carbon::now()->subDay())->where('status','=','pending')->count('status');
    }
}
