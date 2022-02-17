<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\DrcrMemo
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $type_of_memo
 * @property string $reference_number
 * @property string $transaction_category_id
 * @property string $amount
 * @property string $currency_id
 * @property string|null $category
 * @property string|null $description
 * @property string|null $remarks
 * @property string $status
 * @property string|null $created_by
 * @property string|null $approved_by
 * @property string|null $declined_by
 * @property string|null $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $approved_at
 * @property string|null $declined_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $control_number_id
 * @property-read \App\Models\UserAccount|null $user_account
 * @property-read \App\Models\UserBalanceInfo|null $user_balance_info
 * @property-read \App\Models\UserDetail|null $user_details
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo newQuery()
 * @method static \Illuminate\Database\Query\Builder|DrcrMemo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo query()
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereControlNumberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereDeclinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereDeclinedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereTypeOfMemo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DrcrMemo whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|DrcrMemo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DrcrMemo withoutTrashed()
 * @mixin \Eloquent
 */
class DrcrMemo extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $table = 'drcr_memos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_account_id',
        'type_of_memo',
        'reference_number',
        'transaction_category_id',
        'amount',
        'currency_id',
        'category',
        'description',
        'remarks',
        'status',
        'created_by',
        'approved_by',
        'declined_by',
        'approved_at',
        'declinet_at',
        'user_created',
        'user_updated',
        'recipient_number',
    ];

    public function user_account() {
        return $this->hasOne(UserAccount::class, 'id', 'user_account_id');
    }

    public function user_details() {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'user_account_id');
    }

    public function user_balance_info() {
        return $this->hasOne(UserBalanceInfo::class, 'user_account_id', 'user_account_id');
    }

}
