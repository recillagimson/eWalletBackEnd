<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\InReceiveMoney
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $sender_id
 * @property string $reference_number
 * @property string $out_send_money_reference_number
 * @property string $amount
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
 * @property-read \App\Models\UserAccount|null $sender
 * @property-read \App\Models\UserDetail|null $sender_details
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney newQuery()
 * @method static \Illuminate\Database\Query\Builder|InReceiveMoney onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney query()
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereOutSendMoneyReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereTransactionRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InReceiveMoney whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|InReceiveMoney withTrashed()
 * @method static \Illuminate\Database\Query\Builder|InReceiveMoney withoutTrashed()
 * @mixin \Eloquent
 */
class InReceiveMoney extends Model
{
   use HasFactory, UsesUuid, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $fillable = [
        'user_account_id',
        'sender_id',
        'reference_number',
        'out_send_money_reference_number',
        'amount',
        'message',
        'transaction_date',
        'transaction_category_id',
        'transaction_remarks',
        'status',
        'user_created',
        'user_updated',
    ];

    public function sender() {
        return $this->hasOne(UserAccount::class, 'id', 'sender_id');
    }

    public function sender_details() {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'sender_id');
    }


}
