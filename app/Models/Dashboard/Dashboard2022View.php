<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Dashboard\Dashboard2022View
 *
 * @property float|null $total_collection_year
 * @property int|null $total_member
 * @property int|null $total_member_daily
 * @property int|null $total_member_weekly
 * @property int|null $total_member_yearly
 * @property int|null $total_failed_transaction
 * @property int|null $total_failed_daily
 * @property int|null $total_failed_weekly
 * @property int|null $total_failed_yearly
 * @property int|null $total_pending_transaction
 * @property int|null $total_pending_daily
 * @property int|null $total_pending_weekly
 * @property int|null $total_pending_yearly
 * @property int|null $total_success_transaction
 * @property int|null $total_success_daily
 * @property int|null $total_success_weekly
 * @property int|null $total_success_yearly
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View query()
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalCollectionYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalFailedDaily($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalFailedTransaction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalFailedWeekly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalFailedYearly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalMember($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalMemberDaily($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalMemberWeekly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalMemberYearly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalPendingDaily($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalPendingTransaction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalPendingWeekly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalPendingYearly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalSuccessDaily($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalSuccessTransaction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalSuccessWeekly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Dashboard2022View whereTotalSuccessYearly($value)
 * @mixin \Eloquent
 */
class Dashboard2022View extends Model
{
    use HasFactory;

    protected $table = 'dashboard_2022';
}
