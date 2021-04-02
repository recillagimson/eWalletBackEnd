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
        'status',
        'transaction_date',
        'transaction_remarks'
    ];

    
}
