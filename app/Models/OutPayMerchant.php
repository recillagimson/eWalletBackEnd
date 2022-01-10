<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutPayMerchant extends Model
{
    use UsesUuid, HasFactory;

    protected $fillable = [
        'user_account_id',
        'merchant_account_number',
        'reference_number',
        'amount',
        'service_fee',
        'service_fee_id',
        'total_amount',
        'transaction_date',
        'transaction_category_id',
        'description',
        'status',
        'remarks',
        'user_created',
        'user_updated',
    ];
}
