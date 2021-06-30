<?php

namespace App\Services\TempUserDetail;

use App\Enums\OtpTypes;
use App\Models\TempUserDetail;
use App\Traits\Errors\WithAuthErrors;
use App\Traits\Errors\WithUserErrors;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Repositories\UserUtilities\TempUserDetail\ITempUserDetailRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use App\Enums\TempUserDetailStatuses;

class TempUserDetailService implements ITempUserDetailService
{
    use WithUserErrors, WithAuthErrors;

    private ITempUserDetailRepository $tempUserDetail;
    private IUserAccountRepository $userAccount;
    private IUserDetailRepository $userDetail;

    public function __construct(ITempUserDetailRepository $tempUserDetail, IUserAccountRepository $userAccount, IUserDetailRepository $userDetail)
    {
        $this->tempUserDetail = $tempUserDetail;
        $this->userAccount = $userAccount;
        $this->userDetail = $userDetail;
    }

    public function getAllPaginated($perPage = 10)
    {
        $result = $this->tempUserDetail->getAllPaginated($perPage);
        
        return $result;
    }

    public function findById(string $id) 
    {

        $result = $this->tempUserDetail->get($id);

        if(!$result) {
            throw ValidationException::withMessages([
                'user_not_found' => 'Temp User Detail not found'
            ]);
        }

        return $result;
    }

    public function updateStatus(string $id, $status, $user) 
    {
        $tempUserDetail = $this->findById($id);

        if($tempUserDetail->status == TempUserDetailStatuses::approved) {
            throw ValidationException::withMessages([
                'temp_user_already_solved' => 'Temp User Detail already resolved.'
            ]);
        }

        if($tempUserDetail->status == TempUserDetailStatuses::denied) {
            throw ValidationException::withMessages([
                'temp_user_already_declined' => 'Temp User Detail already declined.'
            ]);
        }

        $userAccount = $this->userAccount->get($tempUserDetail->user_account_id);

        if(!$userAccount) {
            throw ValidationException::withMessages([
                'user_not_found' => 'User Account not found'
            ]);
        }

        $userDetail = $userAccount->profile;

        if ($status == 1) {
            $this->userDetail->update($userDetail, array_filter($tempUserDetail->toArray()));
            $this->userAccount->update($userAccount, [
                'mobile_number' => $tempUserDetail->mobile_number,
                'email' => $tempUserDetail->email
            ]);

            $data['approved_by'] = $user->id;
            $data['approved_date'] = Carbon::now();
        } else {
            $data['declined_by'] = $user->id;
            $data['declined_date'] = Carbon::now();
        }


        $data['status'] = $status == 1 ? TempUserDetailStatuses::approved : TempUserDetailStatuses::denied;
        
        $this->tempUserDetail->update($tempUserDetail, $data);
        
        return $tempUserDetail;
    }
}
