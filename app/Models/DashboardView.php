<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DashboardView extends Model
{
    use HasFactory;

    protected $table = 'admin_dashboard_view';

    // profile balanceInfo tier
    public function profile(): HasOne
    {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'id');
    }

    public function balanceInfo(): HasOne
    {
        return $this->hasOne(UserBalanceInfo::class, 'user_account_id', 'id');
    }

    public function tier(): HasOne
    {
        return $this->hasOne(Tier::class, 'id', 'tier_id');
    }
}
