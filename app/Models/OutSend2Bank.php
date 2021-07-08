<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutSend2Bank extends Model
{
    use UsesUuid, HasFactory;

    protected $table = 'out_send2banks';

    protected $fillable = [
        'user_account_id',
        'reference_number',
        'bank_code',
        'bank_name',
        'account_name',
        'account_number',
        'sender_recepient_to',
        'purpose',
        'other_purpose',
        'amount',
        'service_fee',
        'service_fee_id',
        'total_amount',
        'transaction_date',
        'transaction_category_id',
        'transaction_remarks',
        'status',
        'provider',
        'provider_reference',
        'send_receipt_to',
        'remarks',
        'particulars',
        'user_created',
        'user_updated',
        'provider_remittance_id',
        'transaction_response'
    ];

    protected $casts = [
        'transaction_date' => 'datetime'
    ];
}
