<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\OutPayBills
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $account_number
 * @property string $reference_number
 * @property string $amount
 * @property string $other_charges
 * @property string $service_fee
 * @property string $total_amount
 * @property \Illuminate\Support\Carbon $transaction_date
 * @property string $transaction_category_id
 * @property string|null $transaction_remarks
 * @property string|null $message
 * @property string $status
 * @property string $client_reference
 * @property string $billers_code
 * @property string $billers_name
 * @property string $biller_reference_number
 * @property string|null $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\UserAccount|null $user_account
 * @property-read \App\Models\UserDetail|null $user_detail
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills newQuery()
 * @method static \Illuminate\Database\Query\Builder|OutPayBills onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills query()
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereBillerReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereBillersCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereBillersName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereClientReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereOtherCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereServiceFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereTransactionRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutPayBills whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|OutPayBills withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OutPayBills withoutTrashed()
 * @mixin \Eloquent
 */
class OutPayBills extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_account_id',
        'account_number',
        'reference_number',
        'amount',
        'other_charges',
        'service_fee',
        // 'service_fee_id',
        'total_amount',
        'transaction_date',
        'transaction_category_id',
        'transaction_remarks',
        'message',
        'status',
        'client_reference',
        'billers_code',
        'billers_name',
        'biller_reference_number',
        'user_created',
        'user_updated'
    ];

    protected $casts = ['transaction_date' => 'datetime'];

    public function user_detail() {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'user_account_id');
    }

    public function user_account() {
        return $this->hasOne(UserAccount::class, 'id', 'user_account_id');
    }

}
