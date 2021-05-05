<?php

namespace App\Models;

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
        "user_created",
        "user_updated",
    ];

    public function transaction_category() {
        return $this->hasOne(TransactionCategory::class, 'id', 'transaction_category_id');
    }


    // Attributes
    public function getSignedTotalAmountAttribute() {
        // dd($this->transaction_category);
        $signed_transaction = $this->transaction_category->old_transaction_category_id;
        if($signed_transaction === "Positive Value") {
            return "+" . $this->total_amount;
        }
        return "-" . $this->total_amount;
    }

    public function getTransactionTypeAttribute() {
        $signed_transaction = $this->transaction_category->old_transaction_category_id;
        if($signed_transaction === "Positive Value") {
            return "RECEIVED";
        }
        return "SENT";
    }
}
