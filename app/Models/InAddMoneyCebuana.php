<?php

namespace App\Models;

use App\Models\UserUtilities\UserDetail;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\InAddMoneyCebuana
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $reference_number
 * @property string $amount
 * @property string $service_fee
 * @property string $service_fee_id
 * @property string $total_amount
 * @property string $transaction_date
 * @property string $expiration_date
 * @property string $transaction_category_id
 * @property string $transaction_remarks
 * @property string $status
 * @property string $cebuana_reference
 * @property string $posted_date
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\UserAccount|null $user_account
 * @property-read UserDetail|null $user_detail
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana newQuery()
 * @method static \Illuminate\Database\Query\Builder|InAddMoneyCebuana onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana query()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereCebuanaReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana wherePostedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereServiceFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereServiceFeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereTransactionRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyCebuana whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|InAddMoneyCebuana withTrashed()
 * @method static \Illuminate\Database\Query\Builder|InAddMoneyCebuana withoutTrashed()
 * @mixin \Eloquent
 */
class InAddMoneyCebuana extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $table = 'in_add_money_cebuana';

    protected $fillable = [
        "user_account_id",
        "reference_number",
        "amount",
        "service_fee_id",
        "service_fee",
        "total_amount",
        "transaction_date",
        "expiration_date",
        "transaction_category_id",
        "transaction_remarks",
        "status",
        "cebuana_reference",
        "posted_date",
        "user_created",
        "user_updated",
    ];

    public function user_account() {
        return $this->hasOne(UserAccount::class, 'id', 'user_account_id');
    }
    public function user_detail() {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'user_account_id');
    }
}
