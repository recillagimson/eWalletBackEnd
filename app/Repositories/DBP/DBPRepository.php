<?php

namespace App\Repositories\DBP;

use App\Enums\DBPReport;
use App\Repositories\Repository;
use App\Models\DBP\DBPCustomerList;

class DBPRepository extends Repository implements IDBPRepository
{
    public function __construct(DBPCustomerList $model)
    {
        parent::__construct($model);
    }

    public function customerList($from, $to, $filterBy, $filterValue, $isPaginated = false) {
        $result = DBPCustomerList::with([]);
        $result = $result->where('original_created_at', '>=', $from)
        ->whereDate('original_created_at', '<=', $to);

        if($filterBy && $filterValue) {
            // Account Number
            if($filterBy == DBPReport::accountNumber) {
                $result = $result->where('account_number', $filterValue);
            }
            // NAME
            if($filterBy == DBPReport::name) {
                $result = $result->where(function($q) use($filterValue) {
                    $q->where('first_name', 'LIKE', '%' . $filterValue . '%')
                      ->orWhere('middle_name', 'LIKE', '%' . $filterValue . '%')
                      ->orWhere('last_name', 'LIKE', '%' . $filterValue . '%');
                });
            }
            // ACCOUNT STATUS
            if($filterBy == DBPReport::accountStatus) {
                $result = $result->where('account_status', $filterValue);
            }
            // PROFILE STATUS
            if($filterBy == DBPReport::profileStatus) {
                $result = $result->where('profile_status', $filterValue);
            }
        }


        if($isPaginated) {
            return $result->paginate();
        }
        return $result->get();
    }
}
