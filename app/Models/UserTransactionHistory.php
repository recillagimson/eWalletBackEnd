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
}
