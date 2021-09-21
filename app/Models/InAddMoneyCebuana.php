<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InAddMoneyCebuana extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $table = 'in_add_money_cebuana';

    protected $fillable = [
        "user_account_id",
        "reference_number",
        "amount",
        "service_fee_id",
        "service_fee",
        "total_amount",
        "transaction_date",
        "expiration_date",
        "transaction_category_id",
        "transaction_remarks",
        "status",
        "cebuana_reference",
        "posted_date",
        "user_created",
        "user_updated",
    ];
}
