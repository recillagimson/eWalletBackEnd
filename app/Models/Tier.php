<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Tier
 *
 * @property string $id
 * @property string $name
 * @property string $tier_class
 * @property string $account_status
 * @property string $daily_limit
 * @property string $daily_threshold
 * @property string $monthly_limit
 * @property string $monthly_threshold
 * @property int $status
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Tier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tier newQuery()
 * @method static \Illuminate\Database\Query\Builder|Tier onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Tier query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereAccountStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereDailyLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereDailyThreshold($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereMonthlyLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereMonthlyThreshold($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereTierClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tier whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|Tier withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Tier withoutTrashed()
 * @mixin \Eloquent
 */
class Tier extends Model
{
    use UsesUuid;
    use HasFactory, SoftDeletes;

    protected $table = 'tiers';

    protected $fillable = [
        "name",
        "tier_class",
        "account_status",
        "daily_limit",
        "daily_threshold",
        "monthly_limit",
        "monthly_threshold",
        "status",
        "user_created",
        "user_updated",
    ];
}
