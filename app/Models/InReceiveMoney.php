<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InReceiveMoney extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $fillable = [
        'user_account_id',
        'sender_id',
        'reference_number',
        'amount',
        'message',
        'transaction_date',
        // 'transaction_category_id',
        'transaction_remarks',
        'status',
        'user_created',
        'user_updated',
    ];

    
}
