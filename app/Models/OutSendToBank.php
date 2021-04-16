<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class OutSendToBank extends Model
{
    use HasFactory, UsesUuid;

    protected $fillable = [
        'user_account_id', 
        'online_bank_or_over_the_counter_list_id', 
        'reference_number', 
        'account_name', 
        'account_number', 
        'sender_recepient_to', 
        'purpose', 
        'amount', 
        'service_fee', 
        'service_fee_id', 
        'total_amount', 
        'transaction_date', 
        'transaction_category_id', 
        'transaction_remarks',
        'status',
        'pesonet_reference',
        'instapay_reference',
    ];
}
