<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OutPayMerchant
 *
 * @property string $id
 * @property string $user_account_id
 * @property string|null $merchant_account_number
 * @property string $reference_number
 * @property string $amount
 * @property string|null $service_fee_id
 * @property string $service_fee
 * @property string $total_amount
 * @property string $transaction_date
 * @property string $transaction_category_id
 * @property string|null $description
 * @property string $status
 * @property string|null $remarks
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant query()
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereMerchantAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereServiceFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereServiceFeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayMerchant whereUserUpdated($value)
 * @mixin \Eloquent
 */
class OutPayMerchant extends Model
{
    use UsesUuid, HasFactory;

    protected $fillable = [
        'user_account_id',
        'merchant_account_number',
        'reference_number',
        'amount',
        'service_fee',
        'service_fee_id',
        'total_amount',
        'transaction_date',
        'transaction_category_id',
        'description',
        'status',
        'remarks',
        'user_created',
        'user_updated',
    ];
}
