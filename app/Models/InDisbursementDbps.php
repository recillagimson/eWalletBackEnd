<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InDisbursementDbps
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $reference_number
 * @property string $out_disbursement_dbps_reference_number
 * @property float $total_amount
 * @property string $status
 * @property string $transaction_date
 * @property string $transaction_category_id
 * @property string $transaction_remarks
 * @property string $disbursed_by
 * @property string $user_created
 * @property string $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps query()
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps whereDisbursedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps whereOutDisbursementDbpsReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps whereTransactionRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InDisbursementDbps whereUserUpdated($value)
 * @mixin \Eloquent
 */
class InDisbursementDbps extends Model
{
    use HasFactory, UsesUuid;

    protected $table = 'in_disbursement_dbps';

    protected $fillable = [
        "user_account_id",
        "reference_number",
        "total_amount",
        "status",
        "transaction_date",
        "transaction_category_id",
        "transaction_remarks",
        "disbursed_by",
        "user_created",
        "user_updated",
        "out_disbursement_dbps_reference_number"
    ];
}
