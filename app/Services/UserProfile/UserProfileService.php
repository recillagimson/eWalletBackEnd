<?php

namespace App\Services\UserProfile;

use App\Traits\HasFileUploads;
use Illuminate\Validation\ValidationException;
use App\Repositories\UserPhoto\IUserPhotoRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Repositories\UserUtilities\TempUserDetail\ITempUserDetailRepository;
use Carbon\Carbon;
use App\Repositories\UserUtilities\Nationality\INationalityRepository;
use App\Repositories\UserUtilities\NatureOfWork\INatureOfWorkRepository;
use App\Repositories\UserUtilities\SourceOfFund\ISourceOfFundRepository;
use App\Models\UserAccount;

class UserProfileService implements IUserProfileService
{
    use HasFileUploads;
    
    public IUserDetailRepository $userDetailRepository;
    public IUserPhotoRepository $userPhotoRepository;

    public function __construct(IUserDetailRepository $userDetailRepository, 
                                IUserAccountRepository $userAccountRepository,
                                IUserPhotoRepository $userPhotoRepository,
                                ITempUserDetailRepository $tempUserDetail,
                                INationalityRepository $nationality,
                                INatureOfWorkRepository $natureOfWork,
                                ISourceOfFundRepository $sourceOfFund)
    {
        $this->userAccountRepository = $userAccountRepository;
        $this->userDetailRepository = $userDetailRepository;
        $this->userPhotoRepository = $userPhotoRepository;
        $this->tempUserDetail = $tempUserDetail;
        $this->nationality = $nationality;
        $this->natureOfWork = $natureOfWork;
        $this->sourceOfFund = $sourceOfFund;

    }

    public function update(object $userAccount, array $details) {
       $userProfile = $this->userDetailRepository->getByUserId($userAccount->id);
       $details["user_account_id"]= $userAccount->id;
       $details["user_account_status"]= $userAccount->status;
       $data = $this->addUserInput($details, $userAccount, $userProfile);

       $response = (!$userProfile) ? 
       $this->userDetailRepository->create($data)->toArray() : 
       $this->userDetailRepository->update($userProfile, $data);

        return (!$userProfile) ? $response : 
        $this->userDetailRepository->getByUserId($userAccount->id)->toArray();
    }

    public function addUserInput(array $details, object $userAccount, object $data=null) {

        if(!$data) {
            $details['user_created'] = $userAccount->id;
            $details['user_updated'] = $userAccount->id;
        }else {
            $details['user_updated'] = $userAccount->id;
        }
        return $details;
    }

    public function changeAvatar(array $data) {
        // Delete existing first
        // Get details first 
        $userDetails = $this->userDetailRepository->getByUserId(request()->user()->id);
        // If no user Details
        if(!$userDetails) {
            throw ValidationException::withMessages([
                'user_detail_not_found' => 'User Detail not found'
            ]);
        }
        // Delete file using path from current detail
        $this->deleteFile($userDetails->avatar_loction);

        // For Serfile Processing
        // GET EXT NAME
        $avatarPhotoExt = $this->getFileExtensionName($data['avatar_photo']);
        // GENERATE NEW FILE NAME
        $avatarPhotoName = request()->user()->id . "/" . \Str::random(40) . "." . $avatarPhotoExt;
        // PUT FILE TO STORAGE
        $avatarPhotoPath = $this->saveFile($data['avatar_photo'], $avatarPhotoName, 'avatar_photo');
        // SAVE AVATAR LOCATION ON USER DETAILS
        $record = $this->userPhotoRepository->updateAvatarPhoto($avatarPhotoPath);

        return $record;
        // return to controller all created records
    }

    public function updateUserProfile($id, array $request, object $user) 
    {
        $userAccount = $this->userAccountRepository->get($id);

        if(!$userAccount) {
            throw ValidationException::withMessages([
                'user_account_not_found' => 'User Account not found'
            ]);
        }
        
        $request = $this->addTransactionInfo($userAccount, $request, $user);
        $request = $this->addUserInput($request, $user);

        $dirty = $this->checkDirty($userAccount, $request);

        if ($dirty) {
            return [
                "status" => 1,
                "data" => $this->tempUserDetail->create($request)
            ];
        }

        $this->userDetailRepository->update($userAccount, $request);

        return [
            "status" => 0,
            "data" => $userAccount
        ];
    }

    public function addTransactionInfo(UserAccount $userAccount, array $request, object $user) 
    {
        $request['transaction_number'] = "PU" . Carbon::now()->format('YmdHi') . rand(0,99999);
        $request['reviewed_by'] = $user->id;
        $request['reviewed_date'] = Carbon::now();
        $request['user_account_id'] = $userAccount->id;
        $request['status'] = 0; //Pending

        return $request;
    }

    public function checkDirty(UserAccount $user, array $request) 
    {
        $userAccount = $user->fill($request);
        $userDetail = $user->profile->fill($request);

        if ($userAccount->isDirty(['email', 'mobile_number'])) {
            return true;
        }

        if ($userDetail->isDirty(['birth_date', 'first_name', 'last_name', 'middle_name'])) {
            return true;
        }

        return false;
    }
}
