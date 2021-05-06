<?php

namespace App\Services\UserProfile;

use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Repositories\UserPhoto\IUserPhotoRepository;
use App\Traits\HasFileUploads;

class UserProfileService implements IUserProfileService
{
    use HasFileUploads;
    
    public IUserDetailRepository $userDetailRepository;
    public IUserPhotoRepository $userPhotoRepository;

    public function __construct(IUserDetailRepository $userDetailRepository, IUserPhotoRepository $userPhotoRepository)
    {
        $this->userDetailRepository = $userDetailRepository;
        $this->userPhotoRepository = $userPhotoRepository;

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

}
