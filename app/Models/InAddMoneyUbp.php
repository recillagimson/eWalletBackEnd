<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InAddMoneyUbp extends Model
{
    use UsesUuid, HasFactory;

    protected $fillable = [
        'user_account_id',
        'reference_number',
        'provider_reference_number',
        'amount',
        'service_fee',
        'service_fee_id',
        'total_amount',
        'status',
        'transaction_response',
        'transaction_date',
        'user_created',
        'user_updated',
    ];
}
