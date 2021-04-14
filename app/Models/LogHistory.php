<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogHistory extends Model
{
    use HasFactory, SoftDeletes;
    use UsesUuid;

    protected $table ='log_histories';

    protected $fillable = [
        "user_account_id",
        "reference_number",
        "squidpay_module",
        "namespace",
        "transaction_date",
        "remarks",
        "operation",
        "user_created",
        "user_updated",
    ];
}