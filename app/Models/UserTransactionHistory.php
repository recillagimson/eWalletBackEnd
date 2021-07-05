<?php

namespace App\Models;

use App\Enums\TransactionCategoryIds;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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


    // Attributes
    public function getSignedTotalAmountAttribute()
    {
        $signedTransaction = $this->transaction_category->transaction_type;
        if ($signedTransaction === "POSITIVE") {
            return "+" . number_format((float)$this->total_amount, 2, '.', '');
        }
        return "-" . number_format((float)$this->total_amount, 2, '.', '');
    }

    public function getTransactionTypeAttribute()
    {
        $signedTransaction = $this->transaction_category->transaction_type;
        if ($signedTransaction === "POSITIVE") {
            return "RECEIVED";
        }
        return "SENT";
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
