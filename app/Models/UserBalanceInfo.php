<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBalanceInfo extends Model
{
    use HasFactory, SoftDeletes;
    use UsesUuid;

    protected $table ='user_balance_info';

    protected $fillable = [
        "user_account_id",
        "currency_id",
        "available_balance",
        "pending_balance",
        "user_created",
        "user_updated",
    ];
}
