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
       $data = $this->inputBody($userAccount, $details, $userProfile);

       return (!$userProfile) ? 
       $this->userDetailRepository->create($data)->toArray() : 
       array($this->userDetailRepository->update($userProfile, $data));
    }

    private function inputBody(object $userAccount, array $details, object $userProfile=null): array {
        $body = array(
                    "entity_id"=>$details['entity_id'],
                    "user_account_id"=>$userAccount->id,
                    "title"=>$details['title'],
                    "lastname"=>$details['lastname'],
                    "firstname"=>$details['firstname'],
                    "middlename"=>$details['middlename'],
                    "name_extension"=>$details['name_extension'],
                    "birthdate"=>$details['birthdate'],
                    "place_of_birth"=>$details['place_of_birth'],
                    "marital_status_id"=>$details['marital_status_id'],
                    "nationality_id"=>$details['nationality_id'],
                    "encoded_nationality"=>$details['encoded_nationality'],
                    "occupation"=>$details['occupation'],
                    "house_no_street"=>$details['house_no_street'],
                    "city"=>$details['city'],
                    "provice_state"=>$details['provice_state'],
                    "municipality"=>$details['municipality'],
                    "country_id"=>$details['country_id'],
                    "postal_code"=>$details['postal_code'],
                    "nature_of_work_id"=>$details['nature_of_work_id'],
                    "encoded_nature_of_work"=>$details['encoded_nationality'],
                    "source_of_fund_id"=>$details['source_of_fund_id'],
                    "encoded_source_of_fund"=>$details['encoded_source_of_fund'],
                    "mother_maidenname"=>$details['mother_maidenname'],
                    "currency_id"=>$details['currency_id'],
                    "signup_host_id"=>$details['signup_host_id'],
                    "verification_status"=>$details['verification_status'],
                    "user_account_status"=>$userAccount->status,
                    "emergency_lock_status"=>$details['emergency_lock_status'],
                    "report_exception_status"=>$details['report_exception_status'],
                );

        if(!$userProfile) {
            $body['user_created'] = $userAccount->id;
            $body['user_updated'] = $userAccount->id;
        }else {
            $body['user_updated'] = $userAccount->id;
        }

        return $body;
    }

}
