<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\InAddMoneyBPI
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $reference_number
 * @property string $account_number
 * @property string|null $bank_name
 * @property float $amount
 * @property string|null $service_fee_id
 * @property float $service_fee
 * @property float $total_amount
 * @property \Illuminate\Support\Carbon $transaction_date
 * @property string $transaction_category_id
 * @property string $transaction_remarks
 * @property string $status
 * @property string $bpi_reference
 * @property string $transaction_response
 * @property string $user_created
 * @property string $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI newQuery()
 * @method static \Illuminate\Database\Query\Builder|InAddMoneyBPI onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI query()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereBpiReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereServiceFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereServiceFeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereTransactionRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereTransactionResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyBPI whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|InAddMoneyBPI withTrashed()
 * @method static \Illuminate\Database\Query\Builder|InAddMoneyBPI withoutTrashed()
 * @mixin \Eloquent
 */
class InAddMoneyBPI extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;


    protected $table = 'in_add_money_bpi';

    protected $fillable = [
        "bank_name",
        "user_account_id",
        "reference_number",
        "amount",
        "service_fee_id",
        "service_fee",
        "total_amount",
        "transaction_date",
        "transaction_category_id",
        "transaction_remarks",
        "status",
        "bpi_reference",
        "transaction_response",
        "user_created",
        "user_updated",
        "account_number"
    ];

    protected $casts = [
        'transaction_date' => 'datetime'
    ];
}
