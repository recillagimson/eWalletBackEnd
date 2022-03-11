<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InAddMoneyEcPay
 *
 * @property string $id
 * @property string $user_account_id
 * @property string|null $reference_number
 * @property string $amount
 * @property string|null $service_fee
 * @property string|null $service_fee_id
 * @property string|null $total_amount
 * @property string $ec_pay_reference_number
 * @property string|null $expiry_date
 * @property string|null $transaction_date
 * @property string $transction_category_id
 * @property string $transaction_remarks
 * @property string $status
 * @property string $user_created
 * @property string $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $transaction_response
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay query()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereEcPayReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereServiceFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereServiceFeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereTransactionRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereTransactionResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereTransctionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyEcPay whereUserUpdated($value)
 * @mixin \Eloquent
 */
class InAddMoneyEcPay extends Model
{
    use UsesUuid, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "in_add_money_ec_pays";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "user_account_id",
        "reference_number",
        "amount",
        "service_fee",
        "service_fee_id",
        "total_amount",
        "ec_pay_reference_number",
        "expiry_date",
        "transaction_date",
        "transction_category_id",
        "transaction_remarks",
        "status",
        "user_created",
        "user_updated",
        "updated_at",
        'transaction_response',
    ];

    protected $casts = [
        'transaction_date' => 'datetime'
    ];
}
