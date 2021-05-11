<?php

namespace App\Services\Tier;

use App\Models\TierApproval;
use App\Repositories\Notification\INotificationRepository;
use App\Repositories\Tier\ITierApprovalRepository;
use Illuminate\Validation\ValidationException;

class TierApprovalService implements ITierApprovalService
{   
    public ITierApprovalRepository $tierApprovalRepository;
    public INotificationRepository $notificationRepository;

    public function __construct(ITierApprovalRepository $tierApprovalRepository, INotificationRepository $notificationRepository)
    {
        $this->tierApprovalRepository = $tierApprovalRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function updateOrCreateApprovalRequest(array $attr) {
        return $this->tierApprovalRepository->updateOrCreateApprovalRequest($attr);
    }

    public function updateStatus(array $attr, TierApproval $tierApproval) {
        \DB::beginTransaction();
        try {
            $this->tierApprovalRepository->update($tierApproval, $attr);
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
}
