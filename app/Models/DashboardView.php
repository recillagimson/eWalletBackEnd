<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\DashboardView
 *
 * @property string|null $paybills_amount
 * @property string|null $paybills_other_charges
 * @property string|null $paybills_service_fee
 * @property int|null $customer_count
 * @property int $total_transaction
 * @property string|null $total_cashin
 * @property int $sendmoney_amount
 * @property int $sendmoney_service_fee
 * @property string|null $total_collection
 * @property string|null $total_disbursement
 * @property string|null $total_available_funds
 * @property-read \App\Models\UserBalanceInfo|null $balanceInfo
 * @property-read \App\Models\UserDetail|null $profile
 * @property-read \App\Models\Tier|null $tier
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardView query()
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardView whereCustomerCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardView wherePaybillsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardView wherePaybillsOtherCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardView wherePaybillsServiceFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardView whereSendmoneyAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardView whereSendmoneyServiceFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardView whereTotalAvailableFunds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardView whereTotalCashin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardView whereTotalCollection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardView whereTotalDisbursement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DashboardView whereTotalTransaction($value)
 * @mixin \Eloquent
 */
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
