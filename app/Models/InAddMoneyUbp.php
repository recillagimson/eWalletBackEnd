<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InAddMoneyUbp
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $reference_number
 * @property string|null $provider_reference_number
 * @property string $amount
 * @property string $service_fee
 * @property string $service_fee_id
 * @property string $total_amount
 * @property string $status
 * @property mixed|null $transaction_response
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string $transaction_date
 * @property string|null $transaction_category_id
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp query()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereProviderReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereServiceFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereServiceFeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereTransactionResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUbp whereUserUpdated($value)
 * @mixin \Eloquent
 */
class InAddMoneyUbp extends Model
{
    use UsesUuid, HasFactory;

    protected $fillable = [
        'user_account_id',
        'reference_number',
        'provider_reference_number',
        'amount',
        'service_fee',
        'service_fee_id',
        'total_amount',
        'status',
        'transaction_response',
        'transaction_date',
        'user_created',
        'user_updated',
    ];
}
