<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\OutDisbursementDbp
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $reference_number
 * @property string $total_amount
 * @property string $status
 * @property string $transaction_date
 * @property string $transaction_category_id
 * @property string|null $transaction_remarks
 * @property string|null $disbursed_by
 * @property string|null $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp newQuery()
 * @method static \Illuminate\Database\Query\Builder|OutDisbursementDbp onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp query()
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp whereDisbursedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp whereTransactionRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutDisbursementDbp whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|OutDisbursementDbp withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OutDisbursementDbp withoutTrashed()
 * @mixin \Eloquent
 */
class OutDisbursementDbp extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_account_id',
        'reference_number',
        'total_amount',
        'status',
        'transaction_date',
        'transaction_category_id',
        'transaction_remarks',
        'disbursed_by',
        'user_created',
        'user_updated'
    ];
    
}
