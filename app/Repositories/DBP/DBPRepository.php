<?php

namespace App\Repositories\DBP;

use App\Enums\DBPReport;
use App\Models\DBP\DBPClaimReport;
use App\Repositories\Repository;
use App\Models\DBP\DBPCustomerList;
use App\Models\DBP\DBPDisbursement;
use App\Models\DBP\DBPMemo;
use App\Models\DBP\DBPOnBoarding;
use App\Models\DBP\DBPTransactionHistories;

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

    public function disbursement($from, $to, $filterBy, $filterValue, $isPaginated = false) {
        $result = DBPDisbursement::with([]);
        $result = $result->where('original_transaction_date', '>=', $from)
        ->whereDate('original_transaction_date', '<=', $to);

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
            // RSBSA NUMBER
            if($filterBy == DBPReport::rsbsaNumber) {
                $result = $result->where('rsbsa_number', $filterValue);
            }
            // STATUS
            if($filterBy == DBPReport::status) {
                $result = $result->where('status', $filterValue);
            }
        }

        if($isPaginated) {
            return $result->paginate();
        }
        return $result->get();
    }

    public function memo($from, $to, $filterBy, $filterValue, $isPaginated = false) {
        $result = DBPMemo::with([]);
        $result = $result->where('original_transaction_date', '>=', $from)
        ->whereDate('original_transaction_date', '<=', $to);

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
            // RSBSA NUMBER
            if($filterBy == DBPReport::rsbsaNumber) {
                $result = $result->where('rsbsa_number', $filterValue);
            }
            // STATUS
            if($filterBy == DBPReport::status) {
                $result = $result->where('status', $filterValue);
            }
            // TYPE
            if($filterBy == DBPReport::type) {
                $result = $result->where('Type', $filterValue);
            }
        }

        if($isPaginated) {
            return $result->paginate();
        }
        return $result->get();
    }

    public function onBoardingList($from, $to, $filterBy, $filterValue, $isPaginated = false) {
        $result = DBPOnBoarding::with([]);
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
            // RSBSA NUMBER
            if($filterBy == DBPReport::rsbsaNumber) {
                $result = $result->where('rsbsa_number', $filterValue);
            }
            // STATUS
            if($filterBy == DBPReport::status) {
                $result = $result->where('status', $filterValue);
            }
        }

        if($isPaginated) {
            return $result->paginate();
        }
        return $result->get();
    }

    public function transactionHistories($from, $to, $filterBy, $filterValue, $isPaginated = false) {
        $result = DBPTransactionHistories::with([]);
        $result = $result->where('original_transaction_date', '>=', $from)
        ->whereDate('original_transaction_date', '<=', $to);

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
            // RSBSA NUMBER
            if($filterBy == DBPReport::rsbsaNumber) {
                $result = $result->where('rsbsa_number', $filterValue);
            }
            // STATUS
            if($filterBy == DBPReport::status) {
                $result = $result->where('status', $filterValue);
            }

            // REFERENCE NUMBER
            if($filterBy == DBPReport::referenceNumber) {
                $result = $result->where('reference_number', $filterValue);
            }
        }

        if($isPaginated) {
            return $result->paginate();
        }
        return $result->get();
    }

    public function claims($from, $to, $filterBy, $filterValue, $isPaginated = false) {
        $result = DBPClaimReport::with([]);
        $result = $result->where('original_transaction_date', '>=', $from)
        ->whereDate('original_transaction_date', '<=', $to);

        if($filterBy && $filterValue) {
            // RSBSA NUMBER
            if($filterBy == DBPReport::rsbsaNumber) {
                $result = $result->where('rsbsa_number', $filterValue);
            }

            // REFERENCE NUMBER
            if($filterBy == DBPReport::referenceNumber) {
                $result = $result->where('reference_number', $filterValue);
            }
        }

        if($isPaginated) {
            return $result->paginate();
        }
        return $result->get();
    }
}
