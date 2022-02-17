<?php

namespace App\Models;

use App\Enums\TransactionCategoryIds;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\UserTransactionHistory
 *
 * @property string $id
 * @property string $user_account_id
 * @property string $transaction_id
 * @property string $reference_number
 * @property string $total_amount
 * @property string $transaction_category_id
 * @property string $user_created
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $transaction_date
 * @property-read mixed $signed_total_amount
 * @property-read mixed $transactable
 * @property-read mixed $transaction_type
 * @property-read \App\Models\TransactionCategory|null $transaction_category
 * @property-read \App\Models\UserAccount|null $user_account
 * @property-read \App\Models\UserDetail|null $user_details
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistory newQuery()
 * @method static \Illuminate\Database\Query\Builder|UserTransactionHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistory whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistory whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistory whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistory whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistory whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistory whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistory whereUserCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistory whereUserUpdated($value)
 * @method static \Illuminate\Database\Query\Builder|UserTransactionHistory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|UserTransactionHistory withoutTrashed()
 * @mixin \Eloquent
 */
class UserTransactionHistory extends Model
{
    use HasFactory, SoftDeletes;
    use UsesUuid;

    protected $appends = ['signed_total_amount', 'transaction_type'];

    protected $table = 'user_transaction_histories';

    protected $fillable = [
        "user_account_id",
        "transaction_id",
        "reference_number",
        "total_amount",
        "transaction_category_id",
        "transaction_date",
        "user_created",
        "user_updated",
    ];

    protected $casts = [
        'transaction_date' => 'datetime'
    ];

    public function transaction_category()
    {
        return $this->hasOne(TransactionCategory::class, 'id', 'transaction_category_id');
    }

    public function user_account() {
        return $this->hasOne(UserAccount::class, 'id', 'user_account_id');
    }

    public function user_details() {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'user_account_id');
    }

    // public function user_account_number() {
    //     return $this->hasOne(UserAccountNumber::class, 'user_account_id', 'user_account_id');
    // }


    // Attributes
    public function getSignedTotalAmountAttribute()
    {
        if($this && $this->transaction_category) {
            $signedTransaction = $this->transaction_category->transaction_type;
            if ($signedTransaction === "POSITIVE") {
                return "+" . number_format((float)$this->total_amount, 2, '.', '');
            }
            return "-" . number_format((float)$this->total_amount, 2, '.', '');
        }
        return $this->amount;
    }

    public function getTransactionTypeAttribute()
    {
        if($this && $this->transaction_category) {
            $signedTransaction = $this->transaction_category->transaction_type;
            if ($signedTransaction === "POSITIVE") {
                return "RECEIVED";
            }
            return "SENT";
        }
        return "";
    }

    public function getTransactableAttribute() {
        $className = $this->transaction_category->transactable;

        // IF SEND/RECEIVE MONEY TO/FROM SQUIDPAY
        // IF RECEIVE MONEY FROM DRAGONPAY

        if($className) {
            $model = app($className);
            // RECEIVE MONEY FROM SQUIDPAY ACCOUNT
            if($this->transaction_category_id === TransactionCategoryIds::receiveMoneyToSquidPayAccount) {
                $record =  $model->with(['sender_details', 'sender'])->find($this->transaction_id);
                return $record;
            }
            // SEND MONEY FROM SQUIDPAY ACCOUNT
            else if($this->transaction_category_id === TransactionCategoryIds::sendMoneyToSquidPayAccount) {
                $record =  $model->with(['receiver_details', 'receiver'])->find($this->transaction_id);
                return $record;
            }
            // ADD MONEY VIA DRAGONPAY
            else if($this->transaction_category_id === TransactionCategoryIds::cashinDragonPay) {
                $record =  $model->with(['user_details'])->find($this->transaction_id);
                return $record;
            }
            return $model->find($this->transaction_id);
        }
        return [];
    }
}
