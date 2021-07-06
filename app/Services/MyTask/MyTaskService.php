<?php

namespace App\Services\MyTask;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

//Repository
use App\Repositories\UserUtilities\TempUserDetail\ITempUserDetailRepository;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Repositories\DrcrMemo\IDrcrMemoRepository;

class MyTaskService implements IMyTaskService
{
    public ITempUserDetailRepository $tempUserDetails;
    public ITierApprovalRepository $tierApproval;
    public IDrcrMemoRepository $drcrMemo;

    public function __construct(ITempUserDetailRepository $tempUserDetails,
    ITierApprovalRepository $tierApproval,
    IDrcrMemoRepository $drcrMemo)

    {
        $this->tempUserDetails = $tempUserDetails;
        $this->tierApproval = $tierApproval;
        $this->drcrMemo = $drcrMemo;
    }

    public function MyTask(string $UserID)
    {
        //Get pending users
        $TempUserDetails = $this->tempUserDetails->getTempUserDetails();
        //Get tier approval
        $TierApproval = $this->tierApproval->getTierApproval();
        //Total DRCR Memo
        $TotalDRCR = $this->drcrMemo->getDRCRMemo();
        //Total DRCR Memo Per User
        $TotalDRCRPerUser = $this->drcrMemo->getPerUser($UserID);

        $arrMyTask = [
            'temp_user_count'   =>  $TempUserDetails,
            'tier_approval' =>  $TierApproval,
            'total_drcr'    =>  $TotalDRCR,
            'drcr_per_user' =>  $TotalDRCRPerUser,
        ];

        return $arrMyTask;
    }
}
