<?php

namespace App\Repositories\UserUtilities\UserDetail;

use App\Models\CustomerView;
use App\Models\UserUtilities\UserDetail;
use App\Models\UserAccount;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class UserDetailRepository extends Repository implements IUserDetailRepository
{
    public function __construct(UserDetail $model)
    {
        parent::__construct($model);
    }

    public function getByUserId(string $userAccountID)
    {
        $record =  $this->model->where('user_account_id', '=', $userAccountID)->first();

        if($record) {
            return $record->append('avatar_link');
        }

        ValidationException::withMessages([
            'user_detail_not_found' => 'User Detail not found'
        ]);
    }

    public function getFarmers($from, $to, $filterBy, $filterValue, $type) {
        $records = CustomerView::where('original_created_at', '>=', $from)
        ->where('original_created_at', '<=', $to);

        if($filterBy && $filterValue) {
            // IF CUSTOMER_ID
            if($filterBy == 'CUSTOMER_ID') {
                $records = $records->where('account_number', $filterValue);
            } 
            // IF CUSTOMER_NAME
            else if ($filterBy == 'CUSTOMER_NAME') {
                $records = $records->where(function($q) use($filterValue) {
                    $q->where('first_name', 'LIKE', '%' . $filterValue . '%')
                      ->orWhere('last_name', 'LIKE', '%' . $filterValue . '%');
                });
            }
            // IF TYPE
            else if($filterBy == 'TYPE') {
                $records = $records->where('Type', $filterValue );
            }
            // IF STATUS
            else if($filterBy == 'STATUS') {
                $records = $records->where('Status', $filterValue);
            }

            // IF RSBSA_NUMBER
            else if($filterBy == 'RSBSA_NUMBER') {
                $records = $records->where('rsbsa_number', $filterValue);
            }
        }

        if($type == 'API') {
            return $records->where('rsbsa_number', '!=', '')->paginate();
        }
        return $records->where('rsbsa_number', '!=', '')->get();
    }

    public function getIsExistingByNameAndBirthday($firstname, $middename, $lastname, $birthday)
    {
        return $this->model
                    ->where('last_name', $firstname)
                    ->where('first_name', $middename)
                    ->where('middle_name', $lastname)
                    ->where('birth_date', $birthday)
                    ->count();
    }
}
