<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InAddMoneyFromBank extends Model
{
    use HasFactory, UsesUuid, SoftDeletes;

    protected $table = 'in_add_money_from_bank';

    protected $fillable = [
        'user_account_id',
        'online_bank_or_over_the_counter_list_id',
        'reference_number',
        'amount',
        'service_fee',
        'service_fee_id',
        'total_amount',
        'dragonpay_reference',
        'dragonpay_channel_reference_number',
        'transaction_date',
        'transaction_category_id',
        'transaction_remarks',
        'status',
        'user_created',
        'user_updated',
        'expires_at',
    ];

    protected $casts = [
        'transaction_date' => 'datetime'
    ];

    public function user_details()
    {
        return $this->hasOne(UserDetail::class, 'user_account_id', 'user_account_id');
    }
}
