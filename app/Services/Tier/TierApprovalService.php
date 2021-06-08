<?php

namespace App\Services\Tier;

use App\Enums\AccountTiers;
use App\Models\TierApproval;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Repositories\UserPhoto\IUserPhotoRepository;
use App\Repositories\Notification\INotificationRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserPhoto\IUserSelfiePhotoRepository;

class TierApprovalService implements ITierApprovalService
{   
    public ITierApprovalRepository $tierApprovalRepository;
    public IUserPhotoRepository $userPhotoRepository;
    public IUserAccountRepository $userAccountRepository;
    public IUserSelfiePhotoRepository $userSelfiePhotoRepository;
    public INotificationRepository $notificationRepository;

    public function __construct(ITierApprovalRepository $tierApprovalRepository, INotificationRepository $notificationRepository, IUserPhotoRepository $userPhotoRepository, IUserSelfiePhotoRepository $userSelfiePhotoRepository, IUserAccountRepository $userAccountRepository)
    {
        $this->tierApprovalRepository = $tierApprovalRepository;
        $this->notificationRepository = $notificationRepository;
        $this->userPhotoRepository = $userPhotoRepository;
        $this->userSelfiePhotoRepository = $userSelfiePhotoRepository;
        $this->userAccountRepository = $userAccountRepository;
    }

    public function updateOrCreateApprovalRequest(array $attr) {
        return $this->tierApprovalRepository->updateOrCreateApprovalRequest($attr);
    }

    public function updateStatus(array $attr, TierApproval $tierApproval) {
        \DB::beginTransaction();
        try {
            $this->tierApprovalRepository->update($tierApproval, $attr);
            if($attr['status'] === 'APPROVED`') {
                $user_account = $this->userAccountRepository->get($tierApproval->user_account_id);
                $this->userAccountRepository->update($user_account, ['tier_id' => AccountTiers::tier2]);
            }
            $this->notificationRepository->create([
                'user_account_id' => $tierApproval->user_account_id,
                'title' => ucfirst(strtolower($attr['status'])) . ' Tier Upgrade Request',
                'description' => $attr['remarks'],
                'status' => 1,
                'user_created' => request()->user()->id,
                'user_updated' => request()->user()->id,
            ]);
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
        $photo = $this->userPhotoRepository->get($attr['user_photo_id']);
        $attr['reviewed_by'] = request()->user()->id;
        $attr['reviewed_date'] = Carbon::now()->format('Y-m-d H:i:s');
        $this->userPhotoRepository->update($photo, $attr);
        $photo = $this->userPhotoRepository->get($attr['user_photo_id']);
        return $photo;
    }

    public function takeSelfieAction(array $attr) {
        $photo = $this->userSelfiePhotoRepository->get($attr['user_selfie_photo_id']);
        $attr['reviewed_by'] = request()->user()->id;
        $attr['reviewed_date'] = Carbon::now()->format('Y-m-d H:i:s');
        $this->userSelfiePhotoRepository->update($photo, $attr);
        $photo = $this->userSelfiePhotoRepository->get($attr['user_selfie_photo_id']);
        return $photo;
    }
}
