<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\OutSendMoney
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $receiver_id
 * @property string $reference_number
 * @property string $amount
 * @property string $service_fee
 * @property string|null $service_fee_id
 * @property string $total_amount
 * @property string|null $purpose_of_transfer_id
 * @property string|null $message
 * @property string $status
 * @property string $transaction_date
 * @property string $transaction_category_id
 * @property string|null $transaction_remarks
 * @property string|null $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\UserAccount|null $receiver
 * @property-read \App\Models\UserDetail|null $receiver_details
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney newQuery()
 * @method static \Illuminate\Database\Query\Builder|OutSendMoney onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney query()
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney wherePurposeOfTransferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereReceiverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereServiceFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereServiceFeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereTransactionRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutSendMoney whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|OutSendMoney withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OutSendMoney withoutTrashed()
 * @mixin \Eloquent
 */
class OutSendMoney extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_account_id',
        'receiver_id',
        'reference_number',
        'amount',
        'service_fee',
        // 'service_fee_id',
        'total_amount',
        // 'purpose_of_transfer_id',
        'message',
        'status',
        'transaction_date',
        'transaction_category_id',
        'transaction_remarks',
        'user_created',
        'user_updated'
    ];

    public function receiver() {
        return $this->hasOne(UserAccount::class, 'id', 'receiver_id');
    }

    public function receiver_details() {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'receiver_id');
    }

}
