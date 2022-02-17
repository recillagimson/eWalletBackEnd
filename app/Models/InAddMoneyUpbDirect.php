<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\InAddMoneyUpbDirect
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $reference_number
 * @property string $total_amount
 * @property string $transaction_date
 * @property string $transaction_category_id
 * @property string $transaction_remarks
 * @property string $status
 * @property string $ubp_reference
 * @property string $transaction_response
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect newQuery()
 * @method static \Illuminate\Database\Query\Builder|InAddMoneyUpbDirect onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect query()
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect whereTransactionRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect whereTransactionResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect whereUbpReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InAddMoneyUpbDirect whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|InAddMoneyUpbDirect withTrashed()
 * @method static \Illuminate\Database\Query\Builder|InAddMoneyUpbDirect withoutTrashed()
 * @mixin \Eloquent
 */
class InAddMoneyUpbDirect extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $table = 'in_add_money_upb_direct';

    protected $fillable = [
        'user_account_id',
        'reference_number',
        'total_amount',
        'transaction_date',
        'transaction_category_id',
        'transaction_remarks',
        'status',
        'upb_reference',
        'transaction_response',
        'user_created',
        'user_updated',
    ];
}
