<?php

namespace App\Models\UserUtilities;

use Carbon\Carbon;
use App\Models\UserAccount;
use App\Models\TransactionCategory;
use App\Enums\TransactionCategoryIds;
use App\Models\UserDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\UserUtilities\UserTransactionHistoryView
 *
 * @property string $user_account_id
 * @property string|null $rsbsa_number
 * @property string|null $account_number
 * @property string $transaction_id
 * @property string|null $reference_number
 * @property float|null $total_amount
 * @property string|null $transaction_category_id
 * @property string|null $user_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $original_transaction_date
 * @property string|null $transaction_date
 * @property string $status
 * @property string|null $provider_reference
 * @property-read mixed $manila_time_transaction_date
 * @property-read mixed $signed_total_amount
 * @property-read mixed $transactable
 * @property-read mixed $transaction_type
 * @property-read TransactionCategory|null $transaction_category
 * @property-read UserDetail|null $user_detail
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView whereOriginalTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView whereProviderReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView whereRsbsaNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransactionHistoryView whereUserUpdated($value)
 * @mixin \Eloquent
 */
class UserTransactionHistoryView extends Model
{
    use HasFactory;

    protected $table = 'transaction_hitories_view';
    protected $appends = [
        'signed_total_amount', 
        'transaction_type',
        'manila_time_transaction_date'
    ];
    // protected $dates = ['original_transaction_date'];

    public function transaction_category()
    {
        return $this->hasOne(TransactionCategory::class, 'id', 'transaction_category_id');
    }

    public function user_detail() {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'user_account_id');
    }

    // Attributes
    public function getManilaTimeTransactionDateAttribute() {
        return Carbon::parse($this->transaction_date)->format('F d, Y h:i A');
    }

    public function getSignedTotalAmountAttribute()
    {
        if ($this && $this->transaction_category) {
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
        if ($this && $this->transaction_category) {
            $signedTransaction = $this->transaction_category->transaction_type;
            if ($signedTransaction === "POSITIVE") {
                return "RECEIVED";
            }
            return "SENT";
        }
        return "";
    }

    public function getTransactableAttribute()
    {
        $className = $this->transaction_category->transactable;

        // IF SEND/RECEIVE MONEY TO/FROM SQUIDPAY
        // IF RECEIVE MONEY FROM DRAGONPAY

        if ($className) {
            $model = app($className);
            // RECEIVE MONEY FROM SQUIDPAY ACCOUNT
            if ($this->transaction_category_id === TransactionCategoryIds::receiveMoneyToSquidPayAccount) {
                $record = $model->with(['sender_details', 'sender'])->find($this->transaction_id);
                return $record;
            } // SEND MONEY FROM SQUIDPAY ACCOUNT
            else if ($this->transaction_category_id === TransactionCategoryIds::sendMoneyToSquidPayAccount) {
                $record = $model->with(['receiver_details', 'receiver'])->find($this->transaction_id);
                return $record;
            } // ADD MONEY VIA DRAGONPAY
            else if ($this->transaction_category_id === TransactionCategoryIds::cashinDragonPay) {
                $record = $model->with(['user_details'])->find($this->transaction_id);
                return $record;
            }
            return $model->find($this->transaction_id);
        }
        return [];
    }
}
