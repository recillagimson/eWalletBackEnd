<?php

namespace App\Services\UserProfile;

use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;


class UserProfileService implements IUserProfileService
{
    
    public IUserDetailRepository $userDetailRepository;

    public function __construct(IUserDetailRepository $userDetailRepository)
    {
        $this->userDetailRepository = $userDetailRepository;

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

}
