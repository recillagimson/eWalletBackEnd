<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBalanceInfo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table ='user_balance_info';

    protected $fillable = [
        "id",
        "user_account_id",
        "currency_id",
        "available_balance",
        "pending_balance",
        "user_created",
        "user_updated",
    ];
}
