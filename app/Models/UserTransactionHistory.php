<?php

namespace App\Models;

use ReflectionClass;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserTransactionHistory extends Model
{
    use HasFactory, SoftDeletes;
    use UsesUuid;

    protected $appends = ['signed_total_amount', 'transaction_type', 'transactable'];

    protected $table = 'user_transaction_histories';
    protected $fillable = [
        "user_account_id",
        "transaction_id",
        "reference_number",
        "total_amount",
        "transaction_category_id",
        "user_created",
        "user_updated",
    ];

    public function transaction_category() {
        return $this->hasOne(TransactionCategory::class, 'id', 'transaction_category_id');
    }


    // Attributes
    public function getSignedTotalAmountAttribute() {
        // dd($this->transaction_category);
        $signedTransaction = $this->transaction_category->old_transaction_category_id;
        if($signedTransaction === "Positive Value") {
            return "+" . $this->total_amount;
        }
        return "-" . $this->total_amount;
    }

    public function getTransactionTypeAttribute() {
        $signedTransaction = $this->transaction_category->old_transaction_category_id;
        if($signedTransaction === "Positive Value") {
            return "RECEIVED";
        }
        return "SENT";
    }

    public function getTransactableAttribute() {
        $className = $this->transaction_category->transactable;
        if($className) {
            $model = app($className);
            return $model->find($this->transaction_id);
        }
        return [];
    }
}
