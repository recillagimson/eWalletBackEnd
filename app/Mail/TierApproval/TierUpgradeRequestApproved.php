<?php

namespace App\Mail\TierApproval;

use App\Models\Tier;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\UserUtilities\UserDetail;
use Illuminate\Contracts\Queue\ShouldQueue;

class TierUpgradeRequestApproved extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $userDetail;

    public function __construct(UserDetail $userDetail, Tier $tier)
    {
        $this->userDetail = $userDetail;
        $this->tier = $tier;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('emails.tier_approval.tier_upgrade_request_approved');
        return $this->view('emails.tier_approval.tier_upgrade_request_approved')
            ->subject('SquidPay - Tier Upgrade')
            ->with([
                'name' => $this->userDetail->first_name . " " . $this->userDetail->last_name,
                'tier' => $this->tier->tier_class,
                'first_name' => $this->userDetail->first_name,
            ]);
    }
}
