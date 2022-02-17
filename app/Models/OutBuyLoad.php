<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\OutBuyLoad
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $total_amount
 * @property \Illuminate\Support\Carbon|null $transaction_date
 * @property string $transaction_category_id
 * @property string|null $transaction_remarks
 * @property string|null $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $expires_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $reference_number
 * @property string|null $atm_reference_number
 * @property string|null $recipient_mobile_number
 * @property string|null $provider
 * @property string|null $product_code
 * @property string|null $transaction_response
 * @property string|null $provider_transaction_id
 * @property string $status
 * @property string $product_name
 * @property string|null $topup_type
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad newQuery()
 * @method static \Illuminate\Database\Query\Builder|OutBuyLoad onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad query()
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereAtmReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereProductCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereProviderTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereRecipientMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereTopupType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereTransactionRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereTransactionResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutBuyLoad whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|OutBuyLoad withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OutBuyLoad withoutTrashed()
 * @mixin \Eloquent
 */
class OutBuyLoad extends Model
{
    use UsesUuid, HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'out_buy_loads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_account_id',
        'reference_number',
        'total_amount',
        'transaction_date',
        'transaction_category_id',
        'atm_reference_number',
        'recipient_mobile_number',
        'provider',
        'product_code',
        'product_name',
        'topup_type',
        'transaction_response',
        'status',
        'user_created',
        'user_updated',
    ];

    protected $casts = [
        'transaction_date' => 'datetime'
    ];
}
