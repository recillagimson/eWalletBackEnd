<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ServiceFee
 *
 * @property string $id
 * @property string $tier_id
 * @property string $transaction_category_id
 * @property string $amount
 * @property string|null $implementation_date
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Tier|null $tier
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceFee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceFee newQuery()
 * @method static \Illuminate\Database\Query\Builder|ServiceFee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceFee query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceFee whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceFee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceFee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceFee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceFee whereImplementationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceFee whereTierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceFee whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceFee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceFee whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceFee whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|ServiceFee withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ServiceFee withoutTrashed()
 * @mixin \Eloquent
 */
class ServiceFee extends Model
{
    use HasFactory, SoftDeletes;
    use UsesUuid;

    protected $table = 'service_fees';

    protected $fillable = [
        "tier_id",
        "transaction_category_id",
        "amount",
        "implementation_date",
        "user_created",
        "user_updated",
    ];

    public function tier() {
        return $this->hasOne(Tier::class, 'id', 'tier_id');
    }
}
