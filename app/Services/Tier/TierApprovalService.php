<?php

namespace App\Services\Tier;

use App\Enums\AccountTiers;
use App\Models\TierApproval;
use App\Repositories\IdType\IIdTypeRepository;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Repositories\UserPhoto\IUserPhotoRepository;
use App\Repositories\Notification\INotificationRepository;
use App\Repositories\Tier\ITierApprovalCommentRepository;
use App\Repositories\Tier\ITierRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserPhoto\IUserSelfiePhotoRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Notifications\SMS\ISmsService;

class TierApprovalService implements ITierApprovalService
{   
    public ITierApprovalRepository $tierApprovalRepository;
    public IUserPhotoRepository $userPhotoRepository;
    public IUserAccountRepository $userAccountRepository;
    public IUserSelfiePhotoRepository $userSelfiePhotoRepository;
    public INotificationRepository $notificationRepository;
    public IEmailService $emailService;
    public ITierRepository $tierRepository;
    public IUserDetailRepository $userDetailRepository;
    public ISmsService $smsService;
    public ITierApprovalCommentRepository $tierApprovalComment;
    public IIdTypeRepository $idTypeRepository;

    public function __construct(ITierApprovalRepository $tierApprovalRepository, INotificationRepository $notificationRepository, IUserPhotoRepository $userPhotoRepository, IUserSelfiePhotoRepository $userSelfiePhotoRepository, IUserAccountRepository $userAccountRepository, IEmailService $emailService, IUserDetailRepository $userDetailRepository, ITierRepository $tierRepository, ISmsService $smsService, ITierApprovalCommentRepository $tierApprovalComment, IIdTypeRepository $idTypeRepository)
    {
        $this->tierApprovalRepository = $tierApprovalRepository;
        $this->notificationRepository = $notificationRepository;
        $this->userPhotoRepository = $userPhotoRepository;
        $this->userSelfiePhotoRepository = $userSelfiePhotoRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->emailService = $emailService;
        $this->userDetailRepository = $userDetailRepository;
        $this->tierRepository = $tierRepository;
        $this->smsService = $smsService;
        $this->tierApprovalComment = $tierApprovalComment;
        $this->idTypeRepository = $idTypeRepository;
    }

    public function updateOrCreateApprovalRequest(array $attr) {
        return $this->tierApprovalRepository->updateOrCreateApprovalRequest($attr);
    }

    public function updateStatus(array $attr, TierApproval $tierApproval) {
        \DB::beginTransaction();
        try {
            $this->tierApprovalRepository->update($tierApproval, $attr);
            $user_account = $this->userAccountRepository->get($tierApproval->user_account_id);
            $this->notificationRepository->create([
                'user_account_id' => $tierApproval->user_account_id,
                'title' => ucfirst(strtolower($attr['status'])) . ' Tier Upgrade Request',
                'description' => $attr['remarks'],
                'status' => 1,
                'user_created' => request()->user()->id,
                'user_updated' => request()->user()->id,
            ]);
            $tier = $this->tierRepository->get(AccountTiers::tier2);
            $details = $this->userDetailRepository->getByUserId($tierApproval->user_account_id);
            if($attr['status'] === 'APPROVED') {
                $this->userAccountRepository->update($user_account, [
                    'tier_id' => AccountTiers::tier2,
                ]);

                $this->tierApprovalRepository->update($tierApproval, [
                    'approved_by' => $attr['actioned_by'],
                    'approved_date' => Carbon::now()->format('Y-m-d H:i:s')
                ]);

                // if($user_account->mobile_number) {
                //     // SMS USER FOR NOTIFICATION
                //     $this->smsService->tierUpgradeNotification($user_account->mobile_number, $details, $tier);
                // }

                // if($user_account->email) {
                //     // EMAIL USER FOR NOTIFICATION
                //     $this->emailService->tierUpgradeNotification($user_account->email, $details, $tier);
                // }                
            } else if($attr['status'] === 'DECLINED') {

                $this->tierApprovalRepository->update($tierApproval, [
                    'declined_by' => $attr['actioned_by'],
                    'declined_date' => Carbon::now()->format('Y-m-d H:i:s')
                ]);
            }

            \DB::commit();
            return $tierApproval;
        } catch (\Exception $e) {
            \DB::rollBack();
            throw ValidationException::withMessages([
                'unable_to_change_tier' => $e->getMessage()
            ]);
        }

    }

    public function takePhotoAction(array $attr) {
        \DB::beginTransaction();
        try {
            $photo = $this->userPhotoRepository->get($attr['user_photo_id']);
            $attr['reviewed_by'] = request()->user()->id;
            $attr['reviewed_date'] = Carbon::now()->format('Y-m-d H:i:s');
            $this->userPhotoRepository->update($photo, $attr);
            if($photo) {
                $idType = $this->idTypeRepository->get($photo->id_type_id);
                if($idType) {
                    $remarks = $attr['approval_status'] == 'APPROVED' ? $idType->description . ' Approved'  : $idType->description . ' Declined';
                    $this->tierApprovalComment->create([
                        'tier_approval_id' => $photo->tier_approval_id,
                        'remarks' => $remarks,
                        'user_created' => request()->user()->id,
                        'user_updated' => request()->user()->id,
                    ]);
                } else {
                    throw ValidationException::withMessages([
                        'id_type_id_not_found' => 'ID Type not found'
                    ]);
                }
                $photo = $this->userPhotoRepository->get($attr['user_photo_id']);
            } else {
                throw ValidationException::withMessages([
                    'photo_not_found' => 'Photo not found'
                ]);
            }
            \DB::commit();
            return $photo;
        } catch (\Exception $e) {
            \DB::rollBack();
            return $e->getMessage();
        }
    }

    public function takeSelfieAction(array $attr) {
        \DB::beginTransaction();
        try {
            $photo = $this->userSelfiePhotoRepository->get($attr['user_selfie_photo_id']);
            $attr['reviewed_by'] = request()->user()->id;
            $attr['reviewed_date'] = Carbon::now()->format('Y-m-d H:i:s');
            $this->userSelfiePhotoRepository->update($photo, $attr);
            $photo = $this->userSelfiePhotoRepository->get($attr['user_selfie_photo_id']);
            $remarks = $attr['status'] == 'APPROVED' ? "Selfie Approved" : "Selfie Declined";
            $this->tierApprovalComment->create([
                'tier_approval_id' => $photo->tier_approval_id,
                'remarks' => $remarks,
                'user_created' => request()->user()->id,
                'user_updated' => request()->user()->id,
            ]);
            \DB::commit();
            return $photo;
        } catch(\Exception $e) {
            \DB::rollBack();
            return $e->getMessage();
        }
    }
}
