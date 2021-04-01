<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutSendMoney extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_account_id',
        'receiver_id',
        'reference_number',
        'amount',
        'service_fee',
        'total_amount',
        'message',
        'status',
        'transaction_date',
        'transaction_remarks'
    ];

}
