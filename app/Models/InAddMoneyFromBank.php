<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\InAddMoneyFromBank
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $reference_number
 * @property string $amount
 * @property string $service_fee
 * @property string $service_fee_id
 * @property string $total_amount
 * @property string|null $dragonpay_reference
 * @property string|null $dragonpay_channel_reference_number
 * @property \Illuminate\Support\Carbon $transaction_date
 * @property string $transaction_category_id
 * @property string $transaction_remarks
 * @property string $status
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $expired_on
 * @property mixed|null $transaction_response
 * @property-read \App\Models\UserDetail|null $user_details
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank newQuery()
 * @method static \Illuminate\Database\Query\Builder|InAddMoneyFromBank onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank query()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereDragonpayChannelReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereDragonpayReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereExpiredOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereServiceFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereServiceFeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereTransactionRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereTransactionResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyFromBank whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|InAddMoneyFromBank withTrashed()
 * @method static \Illuminate\Database\Query\Builder|InAddMoneyFromBank withoutTrashed()
 * @mixin \Eloquent
 */
class InAddMoneyFromBank extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $table = 'in_add_money_from_bank';

    protected $fillable = [
        'user_account_id',
        'online_bank_or_over_the_counter_list_id',
        'reference_number',
        'amount',
        'service_fee',
        'service_fee_id',
        'total_amount',
        'dragonpay_reference',
        'dragonpay_channel_reference_number',
        'transaction_date',
        'transaction_category_id',
        'transaction_remarks',
        'status',
        'user_created',
        'user_updated',
        'expires_at',
        'expired_on',
        'transaction_response',
    ];

    protected $casts = [
        'transaction_date' => 'datetime'
    ];

    public function user_details()
    {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'user_account_id');
    }
}
