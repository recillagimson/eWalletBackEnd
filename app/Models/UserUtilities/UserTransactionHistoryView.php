<?php

namespace App\Models\UserUtilities;

use App\Models\TransactionCategory;
use App\Enums\TransactionCategoryIds;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserTransactionHistoryView extends Model
{
    use HasFactory;

    protected $table = 'transaction_hitories_view';
    protected $appends = ['signed_total_amount', 'transaction_type'];

    public function transaction_category()
    {
        return $this->hasOne(TransactionCategory::class, 'id', 'transaction_category_id');
    }

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
