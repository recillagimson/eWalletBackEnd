<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\UserBalanceInfo
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $currency_id
 * @property string $available_balance
 * @property string|null $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $pending_balance
 * @method static \Illuminate\Database\Eloquent\Builder|UserBalanceInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserBalanceInfo newQuery()
 * @method static \Illuminate\Database\Query\Builder|UserBalanceInfo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserBalanceInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserBalanceInfo whereAvailableBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBalanceInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBalanceInfo whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBalanceInfo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBalanceInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBalanceInfo wherePendingBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBalanceInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBalanceInfo whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBalanceInfo whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBalanceInfo whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|UserBalanceInfo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|UserBalanceInfo withoutTrashed()
 * @mixin \Eloquent
 */
class UserBalanceInfo extends Model
{
    use HasFactory, SoftDeletes;
    use UsesUuid;

    protected $table ='user_balance_infos';

    protected $fillable = [
        "user_account_id",
        "currency_id",
        "available_balance",
        "pending_balance",
        "user_created",
        "user_updated",
    ];
}
